<?php

namespace Trax\XapiStore\Services\Agent\Actions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Collection;
use Trax\Repo\Querying\Query;
use Trax\XapiStore\Relations\StatementAgent;

trait FilterStatementsMagicActor
{
    /**
     * Agent filtering.
     *
     * @param \Trax\Repo\Querying\Query  $query
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function filterStatementsMagicActor(Query $query): void
    {
        // We can't make a relational request.
        if (!$query->hasFilter('uiActor')
            || !config('trax-xapi-store.requests.relational', false)
        ) {
            return;
        }

        // Only some UI filters support relational requests.
        if (!$this->repository->relationalMagicAgent($query->filter('uiActor'))) {
            return;
        }

        // Get the matching agents.
        $agents = $this->repository->whereUiCombo($query->filter('uiActor'), $query);
        if ($agents->isEmpty()) {
            throw new NotFoundHttpException();
        }
        $agentIds = $agents->pluck('id');

        // Modify the filters.
        $query->removeFilter('uiActor');
        $query->addFilter(['id' => ['$in' => $this->filterStatementsMagicActorCallback($agentIds)]]);
    }

    /**
     * Get callback for agent filter.
     *
     * @param  \Illuminate\Support\Collection  $agentIds
     * @return callable
     */
    protected function filterStatementsMagicActorCallback(Collection $agentIds): callable
    {
        return function ($query) use ($agentIds) {
            return $query->select('statement_id')->from('trax_xapi_statement_agent')
                ->whereIn('agent_id', $agentIds)
                ->where('type', StatementAgent::TYPE_ACTOR);
        };
    }
}
