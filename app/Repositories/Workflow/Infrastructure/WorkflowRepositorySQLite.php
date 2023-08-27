<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

class WorkflowRepositorySQLite extends WorkflowRepositoryBaseSQL
{
    public function maxDurationWorkflow()
    {
        return $this->workflowModel::select('*')
            ->selectRaw('julianday(created_at) - julianday(updated_at) as duration')
            ->where('status', '!=', 'pending')
            ->orderByDesc('duration')
            ->first();
    }
}
