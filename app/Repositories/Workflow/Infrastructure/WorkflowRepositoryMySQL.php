<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

class WorkflowRepositoryMySQL extends WorkflowRepositoryBaseSQL
{
    public function maxDurationWorkflow()
    {
        return $this->workflowModel::select('*')
            ->selectRaw('TIMEDIFF(created_at, updated_at) as duration')
            ->where('status', '!=', 'pending')
            ->orderByDesc('duration')
            ->first();
    }
}
