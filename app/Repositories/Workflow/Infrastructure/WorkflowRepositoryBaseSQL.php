<?php

namespace Waterline\Repositories\Workflow\Infrastructure;

use Waterline\Repositories\Workflow\Interfaces\WorkflowRepositoryInterface;

abstract class WorkflowRepositoryBaseSQL implements WorkflowRepositoryInterface
{
    protected $workflowModel;
    protected $workflowExceptionModel;

    public function __construct()
    {
        $this->workflowModel = config('workflows.stored_workflow_model', \Workflow\Models\StoredWorkflow::class);
        $this->workflowExceptionModel = config('workflows.stored_workflow_exception_model', \Workflow\Models\StoredWorkflowException::class);
    }

    public function flowsPastHour(): int
    {
        return $this->workflowModel::where('updated_at', '>=', now()->subHour())->count();
    }

    public function exceptionsPastHour(): int
    {
        return $this->workflowExceptionModel::where('created_at', '>=', now()->subHour())->count();
    }

    public function failedFlowsPastWeek(): int
    {
        return $this->workflowModel::where('status', 'failed')
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();
    }

    public function maxWaitTimeWorkflow()
    {
        return $this->workflowModel::where('status', 'pending')
            ->orderBy('updated_at')
            ->first();
    }

    public function maxExceptionsWorkflow()
    {
        return $this->workflowModel::withCount('exceptions')
            ->has('exceptions')
            ->orderByDesc('exceptions_count')
            ->orderByDesc('updated_at')
            ->first();
    }

    public function totalFlows(): int
    {
        return $this->workflowModel::count();
    }
}
