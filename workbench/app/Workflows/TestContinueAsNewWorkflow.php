<?php

namespace Workbench\App\Workflows;

use Workflow\ActivityStub;
use Workflow\Workflow;
use Workflow\WorkflowStub;

class TestContinueAsNewWorkflow extends Workflow
{
    public function execute(int $count = 0, int $max = 3)
    {
        yield ActivityStub::make(TestActivity::class);

        if ($count >= $max) {
            return 'Continue as new workflow completed successfully';
        }

        return yield WorkflowStub::continueAsNew($count + 1, $max);
    }
}
