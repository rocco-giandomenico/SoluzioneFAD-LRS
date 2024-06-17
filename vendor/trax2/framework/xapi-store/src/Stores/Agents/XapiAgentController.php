<?php

namespace Trax\XapiStore\Stores\Agents;

use Illuminate\Http\Request;
use Trax\XapiStore\Abstracts\XapiController;
use Trax\XapiStore\Exceptions\XapiAuthorizationException;
use Trax\XapiStore\Stores\Agents\AgentRepository;
use Trax\XapiStore\Stores\Logs\Logger;

class XapiAgentController extends XapiController
{
    use XapiAgentValidation;
    
    /**
     * The repository.
     *
     * @var \Trax\XapiStore\Stores\Agents\AgentRepository
     */
    protected $repository;

    /**
     * The permissions domain.
     *
     * @var string
     */
    protected $permissionsDomain = 'agent';

    /**
     * Create the constructor.
     *
     * @param  \Trax\XapiStore\Stores\Agents\AgentRepository  $repository
     * @return void
     */
    public function __construct(AgentRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Post a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function post(Request $request)
    {
        // Alternate request.
        if ($redirectMethod = $this->checkAlternateRequest($request)) {
            return $this->$redirectMethod($request);
        }
        throw new XapiAuthorizationException('POST request is not allowed on this API.');
    }

    /**
     * Put a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function put(Request $request)
    {
        throw new XapiAuthorizationException('PUT request is not allowed on this API.');
    }

    /**
     * Get resources.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $service = app(\Trax\XapiStore\Services\Agent\AgentService::class);

        // Validate request.
        $xapiRequest = $this->validateGetRequest($request);

        // Perform request.
        $resources = $this->getResources($xapiRequest);
        if ($resource = $resources->last()) {
            // Return the matching Person.
            $person = $service->getRealPerson($resource);
        } else {
            // We generate the result from the request param.
            $person = $service->getVirtualPerson(
                json_decode($xapiRequest->param('agent'))
            );
        }

        // Logging.
        Logger::log($this->permissionsDomain, 'GET');

        // Response.
        return $this->response($person);
    }

    /**
     * Delete a resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Trax\XapiStore\Exceptions\XapiAuthorizationException
     */
    public function delete(Request $request)
    {
        throw new XapiAuthorizationException('DELETE request is not allowed on this API.');
    }
}
