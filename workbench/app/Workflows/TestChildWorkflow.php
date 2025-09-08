<?php

namespace Workbench\App\Workflows;

use Workflow\ActivityStub;
use Workflow\Workflow;

class TestChildWorkflow extends Workflow
{
    public function execute()
    {
        yield ActivityStub::make(TestActivity::class);

        return 'Child workflow completed successfully';
    }
}
