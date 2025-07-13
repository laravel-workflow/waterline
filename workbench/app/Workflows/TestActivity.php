<?php

namespace Workbench\App\Workflows;

use Workflow\Activity;

class TestActivity extends Activity
{
    public function execute()
    {
        return 'result';
    }
}
