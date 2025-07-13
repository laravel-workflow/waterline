<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');

Artisan::command('test:dispatch', function () {
    $this->info('Dispatching TestWorkflow...');

    // Dispatch the workflow using WorkflowStub
    $workflow = \Workflow\WorkflowStub::make(\App\Workflows\TestWorkflow::class);
    $workflowId = $workflow->start();

    $this->info("Workflow dispatched with ID: {$workflowId}");

    // Wait for it to complete
    sleep(3);

    // Check results
    $count = \Workflow\Models\StoredWorkflow::count();
    $this->info("Total workflows: {$count}");

    if ($count > 0) {
        $latest = \Workflow\Models\StoredWorkflow::latest()->first();
        $this->info("Latest workflow: ID={$latest->id}, Status={$latest->status}");
    }
})->purpose('Dispatch a test workflow');
