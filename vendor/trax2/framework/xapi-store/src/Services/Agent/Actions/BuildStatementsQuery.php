<?php

namespace Trax\XapiStore\Services\Agent\Actions;

use Trax\Repo\Querying\Query;

trait BuildStatementsQuery
{
    use FilterStatementsAgent, FilterStatementsRelatedAgents,
        FilterStatementsMagicActor, FilterStatementsMagicContext, FilterStatementsMagicObject;

    /**
     * Statement filtering.
     *
     * @param \Trax\Repo\Querying\Query  $query
     * @return void
     */
    public function buildStatementsQuery(Query $query): void
    {
        $this->filterStatementsAgent($query);
        $this->filterStatementsRelatedAgents($query);
        $this->filterStatementsMagicActor($query);
        $this->filterStatementsMagicObject($query);
        $this->filterStatementsMagicContext($query);
    }
}
