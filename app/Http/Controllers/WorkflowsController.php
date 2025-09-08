<?php

namespace Waterline\Http\Controllers;

use SplFileObject;
use Waterline\Http\Resources\StoredWorkflowResource;
use Waterline\Transformer\WorkflowToChartDataTransformer;
use Workflow\Models\StoredWorkflow;

class WorkflowsController extends Controller
{
    public function completed() {
        return config('workflows.stored_workflow_model', StoredWorkflow::class)::whereIn('status', [
                'completed',
                'continued',
            ])
            ->orderByDesc('id')
            ->paginate(50);
    }

    public function failed() {
        return config('workflows.stored_workflow_model', StoredWorkflow::class)::whereStatus('failed')
            ->orderByDesc('id')
            ->paginate(50);
    }

    public function running() {
        return config('workflows.stored_workflow_model', StoredWorkflow::class)::whereIn('status', [
                'created',
                'pending',
                'running',
                'waiting',
            ])
            ->orderByDesc('id')
            ->paginate(50);
    }

    public function show($id) {
        $flow = config('workflows.stored_workflow_model', StoredWorkflow::class)::with([
            'continuedWorkflows',
            'exceptions',
            'logs',
            'parents'
        ])->find($id);

        return StoredWorkflowResource::make($flow);
    }
}
