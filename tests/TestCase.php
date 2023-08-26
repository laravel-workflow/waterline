<?php

namespace Waterline\Tests;

use Illuminate\Support\Carbon;
use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Waterline\Waterline;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2022-01-01');

        Waterline::auth(function () {
            return true;
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('app.key', 'base64:UTyp33UhGolgzCK5CJmT+hNHcA+dJyp3+oINtX+VoPI=');
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom('./vendor/laravel-workflow/laravel-workflow/src/migrations');
    }

    protected function getPackageProviders($app)
    {
        return ['Waterline\WaterlineServiceProvider'];
    }
}
