<?php

namespace Workbench\App\Workflows;

use Workflow\ActivityStub;
use Workflow\Workflow;

class TestWorkflow extends Workflow
{
    public function execute()
    {
        yield ActivityStub::make(TestActivity::class);

        return 'Workflow completed successfully';
    }
}
