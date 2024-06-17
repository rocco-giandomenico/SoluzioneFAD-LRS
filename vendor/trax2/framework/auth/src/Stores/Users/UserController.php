<?php

namespace Trax\Auth\Stores\Users;

use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Trax\Repo\CrudRequest;
use Trax\Auth\Controllers\CrudController;
use Trax\Auth\Stores\Owners\OwnerRepository;
use Trax\Auth\Stores\Entities\EntityRepository;
use Trax\Auth\Stores\Roles\RoleRepository;
use Trax\Auth\Stores\Users\UserSources;
use Trax\Auth\Authentifier;
use Trax\Auth\Traits\HasOwner;
use Trax\Auth\Traits\HasEntity;
use Trax\Core\Helpers as Trax;

class UserController extends CrudController
{
    use HasOwner, HasEntity;

    /**
     * The resource parameter name.
     *
     * @var string
     */
    protected $routeParameter = 'user';

    /**
     * The owners repository.
     *
     * @var \Trax\Auth\Stores\Owners\OwnerRepository
     */
    protected $owners;

    /**
     * The entities repository.
     *
     * @var \Trax\Auth\Stores\Entities\EntityRepository
     */
    protected $entities;

    /**
     * The roles repository.
     *
     * @var \Trax\Auth\Stores\Roles\RoleRepository
     */
    protected $roles;

    /**
     * Create the constructor.
     *
     * @param  \Trax\Auth\Stores\Users\UserRepository  $users
     * @param  \Trax\Auth\Stores\Owners\OwnerRepository  $owners
     * @param  \Trax\Auth\Stores\Entities\EntityRepository  $entities
     * @param  \Trax\Auth\Stores\Roles\RoleRepository  $roles
     * @return void
     */
    public function __construct(UserRepository $users, OwnerRepository $owners, EntityRepository $entities, RoleRepository $roles)
    {
        parent::__construct();
        $this->repository = $users;
        $this->owners = $owners;
        $this->entities = $entities;
        $this->roles = $roles;
    }
    
    /**
     * Get the validation rules.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return array
     */
    protected function validationRules(Request $request)
    {
        $userTable = $this->repository->table();
        $unicity = $request->method() == 'POST' ? '' : ',' . $request->route('user');
        return [
            'email' => "required|email|unique:$userTable,email$unicity",
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'username' => "required|string|unique:$userTable,username$unicity",
            'password' => 'nullable|string|custom_password',
            'active' => 'boolean',
            'admin' => 'boolean',
            'source' => (new UserSources)->rule(),
            'meta' => 'array',
            'owner_id' => 'nullable|integer|exists:trax_owners,id',
            'entity_id' => 'nullable|integer|exists:trax_entities,id',
            'role_id' => 'nullable|integer|exists:trax_roles,id',
        ];
    }

    /**
     * Validate an update request of authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request;
     * @param  bool  $withContent;
     * @return  \Trax\Repo\CrudRequest
     */
    protected function validateMyRequest(Request $request, bool $withContent = false): CrudRequest
    {
        $params = $request->validate(
            CrudRequest::validationRules()
        );
        $content = !$withContent ? null : $request->validate([
            'firstname' => "string",
            'lastname' => "string",
            'password' => 'nullable|string|custom_password|confirmed',
        ]);
        return new CrudRequest($params, $content);
    }

    /**
     * Display the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Auth\Authentifier  $authentifier
     * @return \Illuminate\Http\Response
     */
    public function showMe(Request $request, Authentifier $authentifier)
    {
        // Validate request.
        $crudRequest = $this->validateMyRequest($request);
        $include = $this->validateIncludeRequest($request);

        // Check permissions.
        $id = $authentifier->user()->id;
        $resource = $this->repository->findOrFail($id, $crudRequest->query());
        $this->authorizer->must($this->permissionsDomain . '.read', $resource);

        // Perform task.
        $responseData = $this->responseData($resource);
        return $this->responseWithIncludedData($responseData, $include);
    }

    /**
     * Update the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Auth\Authentifier  $authentifier
     * @return \Illuminate\Http\Response
     */
    public function updateMe(Request $request, Authentifier $authentifier)
    {
        // Validate request.
        $crudRequest = $this->validateMyRequest($request, true);
        $include = $this->validateIncludeRequest($request);

        // Check permissions.
        $id = $authentifier->user()->id;
        $resource = $this->repository->findOrFail($id, $crudRequest->query());
        $this->authorizer->must($this->permissionsDomain . '.write', $resource);

        // Perform task.
        $resource = $this->repository->updateModel($resource, $crudRequest->content());
        $responseData = $this->responseData($resource);
        return $this->responseWithIncludedData($responseData, $include);
    }

    /**
     * Change my password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Trax\Auth\Authentifier  $authentifier
     * @return \Illuminate\Http\Response
     */
    public function changeMyPassword(Request $request, Authentifier $authentifier)
    {
        // Check permissions.
        // We are always allowed to change our own password.

        // Validate request.
        $data = $request->validate([
            'current_password' => 'required|string|password',
            'new_password' => 'required|string|custom_password|confirmed',
        ]);

        // Perform task.
        $this->repository->updateModel($authentifier->user(), [
            'password' => $data['new_password']
        ]);

        return response('', 204);
    }

    /**
     * Hook before a store or update request.
     *
     * @param  \Trax\Repo\CrudRequest  $crudRequest
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function beforeWrite(CrudRequest $crudRequest, Request $request)
    {
        $this->checkOwner($crudRequest);
        $this->checkEntity($crudRequest);
    }

    /**
     * Hook before a destroy request.
     *
     * @param  \Illuminate\Database\Eloquent\Model
     * @param  \Trax\Repo\CrudRequest  $crudRequest
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function beforeDestroy(Model $resource, CrudRequest $crudRequest, Request $request)
    {
        // We can't delete our own account.
        if ($this->authentifier->isUser() && $this->authentifier->consumer()->id == $resource->id) {
            throw new AuthorizationException("Forbidden: you can't delete your own account.");
        }
    }

    /**
     * Get response complementary data.
     *
     * @param string  $name
     * @return mixed
     */
    protected function includeData(string $name)
    {
        // The owner filter from the request may also apply to complementary data.
        $ownerFilter = $this->ownerCrudRequest();

        switch ($name) {
            case 'owners':
                return Trax::select($this->getResources('owner', $this->owners, $ownerFilter));
            case 'entities':
                return Trax::select($this->getResources('entity', $this->entities, $ownerFilter));
            case 'roles':
                return Trax::select($this->getResources('role', $this->roles, $ownerFilter));
            case 'sources':
                return Trax::select((new UserSources)->all());
            case 'csrf-token':
                return csrf_token();
            case 'config':
                return $this->uiConfig();
        }
    }

    /**
     * Get UI config.
     *
     * @return array
     */
    protected function uiConfig()
    {
        return [
            'xapi' => [
                // No need to give default values here because the Starter Edition does not need it.
                'requests' => config('trax-xapi-store.requests'),
                'processing' => config('trax-xapi-store.processing'),
                'privacy' => config('trax-xapi-store.privacy'),
                'logging' => config('trax-xapi-store.logging'),
            ],
        ];
    }
}
