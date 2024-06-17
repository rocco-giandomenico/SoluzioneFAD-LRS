<?php

namespace Trax\XapiStore\Traits;

use Trax\Repo\Querying\Query;
use Trax\XapiStore\XapiDate;
use Trax\XapiStore\Stores\Agents\AgentFactory;

trait XapiDocumentFilters
{
    use XapiAgentFilter;
    
    /**
     * Filter: stateId.
     *
     * @param  string  $id
     * @param  \Trax\Repo\Querying\Query  $query
     * @return array
     */
    public function stateIdFilter($id, Query $query = null)
    {
        return [
            ['state_id' => $id],
        ];
    }

    /**
     * Filter: profileId.
     *
     * @param  string  $id
     * @param  \Trax\Repo\Querying\Query  $query
     * @return array
     */
    public function profileIdFilter($id, Query $query = null)
    {
        return [
            ['profile_id' => $id],
        ];
    }

    /**
     * Filter: activityId.
     *
     * @param  string  $id
     * @param  \Trax\Repo\Querying\Query  $query
     * @return array
     */
    public function activityIdFilter($id, Query $query = null)
    {
        return [
            ['activity_id' => $id],
        ];
    }

    /**
     * Filter: agent.
     *
     * @param  string|array|object  $agent
     * @param  \Trax\Repo\Querying\Query  $query
     * @return array
     */
    public function agentFilter($agent, Query $query = null)
    {
        return [
            ['vid' => AgentFactory::virtualId($agent)]
        ];
    }

    /**
     * Filter: since.
     *
     * @param  string  $since
     * @param  \Trax\Repo\Querying\Query  $query
     * @return array
     */
    public function sinceFilter($since, Query $query = null)
    {
        return [
            ['timestamp' => ['$gt' => XapiDate::normalize($since)]],
        ];
    }
}
