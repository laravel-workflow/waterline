<?php

namespace Waterline\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    protected $signature = 'waterline:install';

    protected $description = 'Install all of Waterline\'s resources';

    public function handle()
    {
        $this->comment('Publishing Waterline Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'waterline-provider']);

        $this->comment('Publishing Waterline Assets...');
        $this->callSilent('vendor:publish', ['--tag' => 'waterline-assets']);

		$this->comment('Publishing Waterline Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'waterline-config']);

        $this->registerWaterlineServiceProvider();

        $this->info('Waterline installed successfully.');
    }

    /**
     * Register the Waterline service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerWaterlineServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace.'\\Providers\\WaterlineServiceProvider::class')) {
            return;
        }

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\WaterlineServiceProvider::class,".PHP_EOL,
            $appConfig
        ));

        file_put_contents(app_path('Providers/WaterlineServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/WaterlineServiceProvider.php'))
        ));
    }
}
