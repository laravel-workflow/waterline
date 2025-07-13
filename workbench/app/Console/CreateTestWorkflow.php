<?php

namespace Workbench\App\Console;

use Illuminate\Console\Command;

class CreateTestWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflow:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test workflow';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Creating test workflow...');

        try {
            $workflow = \Workflow\WorkflowStub::make(\Workbench\App\Workflows\TestWorkflow::class);
            $workflow->start();

            $count = \Workflow\Models\StoredWorkflow::count();
            $this->info("Workflow created successfully!");
            $this->info("Total workflows: {$count}");

            if ($count > 0) {
                $latest = \Workflow\Models\StoredWorkflow::latest()->first();
                $this->info("Latest workflow ID: {$latest->id}");
                $this->info("Latest workflow status: {$latest->status}");
            }

        } catch (\Exception $e) {
            $this->error("Error creating workflow: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
