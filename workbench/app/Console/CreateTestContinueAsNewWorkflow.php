<?php

namespace Workbench\App\Console;

use Illuminate\Console\Command;

class CreateTestContinueAsNewWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:create-test-continue-as-new';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test continue as new workflow';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating test continue as new workflow...');

        try {
            $workflow = \Workflow\WorkflowStub::make(\Workbench\App\Workflows\TestContinueAsNewWorkflow::class);
            $workflow->start();

            $count = \Workflow\Models\StoredWorkflow::count();
            $this->info("Continue as new workflow created successfully!");
            $this->info("Total workflows: {$count}");

            if ($count > 0) {
                $latest = \Workflow\Models\StoredWorkflow::latest()->first();
                $this->info("Latest workflow ID: {$latest->id}");
                $this->info("Latest workflow status: {$latest->status}");
            }

        } catch (\Exception $e) {
            $this->error("Error creating continue as new workflow: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
