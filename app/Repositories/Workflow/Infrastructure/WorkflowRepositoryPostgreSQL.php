<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

class WorkflowRepositoryPostgreSQL extends WorkflowRepositoryBaseSQL
{
    public function maxDurationWorkflow()
    {
        return $this->workflowModel::select('*')
            ->selectRaw('(EXTRACT(EPOCH FROM created_at - updated_at)) as duration')
            ->where('status', '!=', 'pending')
            ->orderByDesc('duration')
            ->first();
    }
}
