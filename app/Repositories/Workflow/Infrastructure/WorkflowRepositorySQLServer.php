<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

class WorkflowRepositorySQLServer extends WorkflowRepositoryBaseSQL
{
    public function maxDurationWorkflow()
    {
        return $this->workflowModel::select('*')
            ->selectRaw('DATEDIFF(SECOND, created_at, updated_at) as duration')
            ->where('status', '!=', 'pending')
            ->orderByDesc('duration')
            ->first();
    }
}
