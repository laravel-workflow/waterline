<?php

namespace Waterline\Http\Controllers;

use SplFileObject;
use Workflow\Models\StoredWorkflow;
use Workflow\Serializers\Y;

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
        $flow = config('workflows.stored_workflow_model', StoredWorkflow::class)::with(['exceptions', 'logs'])->find($id);

        $flow->arguments = serialize(Y::unserialize($flow->arguments));

        $flow->logs = $flow->logs->map(function ($log) {
            $log->result = serialize(Y::unserialize($log->result));
            return $log;
        });

        $flow->exceptions = $flow->exceptions->map(function ($exception) {
            $exception->code ??= null;
            $unserialized = Y::unserialize($exception->exception);
            if (is_array($unserialized)
                && array_key_exists('class', $unserialized)
                && is_subclass_of($unserialized['class'], \Throwable::class)
                && file_exists($unserialized['file'])
            ) {
                $file = new SplFileObject($unserialized['file']);
                $file->seek($unserialized['line'] - 4);
                for ($line = 0; $line < 7; ++$line) {
                    $exception->code .= $file->current();
                    $file->next();
                    if ($file->eof()) break;
                }
                $exception->code = rtrim($exception->code);
                $exception->exception = serialize($unserialized);
            }
            return $exception;
        });

        $flow->output = $flow->output === null ? serialize(null) : serialize(Y::unserialize($flow->output));

        return $flow;
    }
}
