<?php

namespace Workbench\App\Workflows;

use Workflow\ActivityStub;
use Workflow\ChildWorkflowStub;
use Workflow\Workflow;

class TestParentWorkflow extends Workflow
{
    public function execute()
    {
        yield ActivityStub::make(TestActivity::class);

        yield ChildWorkflowStub::make(TestChildWorkflow::class);

        return 'Parent workflow completed successfully';
    }
}
