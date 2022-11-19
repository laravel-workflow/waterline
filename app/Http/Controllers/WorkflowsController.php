<?php

namespace Waterline\Http\Controllers;

use SplFileObject;
use Workflow\Models\StoredWorkflow;

class WorkflowsController extends Controller
{
    public function completed() {
        return StoredWorkflow::whereStatus('completed')
            ->paginate(50);
    }

    public function failed() {
        return StoredWorkflow::whereStatus('failed')
            ->paginate(50);
    }

    public function running() {
        return StoredWorkflow::whereIn('status', [
                'created',
                'pending',
                'running',
                'waiting',
            ])
            ->paginate(50);
    }

    public function show($id) {
        $flow = StoredWorkflow::whereId($id)->with(['exceptions', 'logs'])->first();

        $flow->exceptions = $flow->exceptions->map(function ($exception) {
            $unserialized = unserialize($exception->exception);
            $file = new SplFileObject($unserialized->getFile());
            $file->seek($unserialized->getLine() - 4);
            for ($line = 0; $line < 7; ++$line) {
                $exception->code .= $file->current();
                $file->next();
                if ($file->eof()) break;
            }
            $exception->code = rtrim($exception->code);
            return $exception;
        });

        return $flow;
    }
}
