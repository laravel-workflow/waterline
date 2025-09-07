<?php

namespace Workbench\App\Console;

use Illuminate\Console\Command;

class CreateTestParentWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:create-test-parent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test parent workflow';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating test parent workflow...');

        try {
            $workflow = \Workflow\WorkflowStub::make(\Workbench\App\Workflows\TestParentWorkflow::class);
            $workflow->start();

            $count = \Workflow\Models\StoredWorkflow::count();
            $this->info("Parent workflow created successfully!");
            $this->info("Total workflows: {$count}");

            if ($count > 0) {
                $latest = \Workflow\Models\StoredWorkflow::latest()->first();
                $this->info("Latest workflow ID: {$latest->id}");
                $this->info("Latest workflow status: {$latest->status}");
            }

        } catch (\Exception $e) {
            $this->error("Error creating parent workflow: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
