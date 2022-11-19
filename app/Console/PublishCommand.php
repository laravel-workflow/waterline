<?php

namespace Waterline\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waterline:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all of Waterline\'s resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            '--tag' => 'waterline-assets',
            '--force' => true,
        ]);
    }
}
