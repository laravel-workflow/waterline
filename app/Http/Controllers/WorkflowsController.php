<?php

namespace Waterline\Http\Controllers;

use SplFileObject;
use Workflow\Models\StoredWorkflow;

class WorkflowsController extends Controller
{
    public function completed() {
        return config('workflows.stored_workflow_model', StoredWorkflow::class)::whereStatus('completed')
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
        $flow = config('workflows.stored_workflow_model', StoredWorkflow::class)::whereId($id)->with(['exceptions', 'logs'])->first();

        $flow->exceptions = $flow->exceptions->map(function ($exception) {
            $unserialized = unserialize($exception->exception);
            if (is_object($unserialized) && method_exists($unserialized, 'getFile')) {
                $file = new SplFileObject($unserialized->getFile());
                $file->seek($unserialized->getLine() - 4);
                for ($line = 0; $line < 7; ++$line) {
                    $exception->code .= $file->current();
                    $file->next();
                    if ($file->eof()) break;
                }
                $exception->code = rtrim($exception->code);    
            }
            return $exception;
        });

        return $flow;
    }
}
