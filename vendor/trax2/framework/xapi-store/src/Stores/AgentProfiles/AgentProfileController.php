<?php

namespace Trax\XapiStore\Stores\AgentProfiles;

use Illuminate\Http\Request;
use Trax\Repo\CrudRequest;
use Trax\Auth\Controllers\CrudController;
use Trax\Auth\Traits\HasOwner;
use Trax\XapiStore\Stores\AgentProfiles\AgentProfileRepository;

class AgentProfileController extends CrudController
{
    use HasOwner;
    
    /**
     * The resource parameter name.
     *
     * @var string
     */
    protected $routeParameter = 'agent_profile';

    /**
     * Create the constructor.
     *
     * @param  \Trax\XapiStore\Stores\AgentProfiles\AgentProfileRepository  $repository
     * @return void
     */
    public function __construct(AgentProfileRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Get the validation rules.
     *
     * @param \Illuminate\Http\Request  $request;
     * @return array
     */
    protected function validationRules(Request $request)
    {
        return [
            'profile_id' => 'required|string',
            'agent' => 'required|xapi_agent',
            'data.content' => 'required',
            'data.type' => 'required|content_type',
            'owner_id' => 'nullable|integer|exists:trax_owners,id',
        ];
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
    }
}
