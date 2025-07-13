<?php

namespace Waterline\Tests;

use Illuminate\Support\Carbon;
use function Orchestra\Testbench\artisan;
use function Orchestra\Testbench\workbench_path;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
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
        $this->app->bind('db.connector.sqlsrv', function () {
            return new class extends \Illuminate\Database\Connectors\SqlServerConnector
            {
                protected $options = [
                    PDO::ATTR_CASE => PDO::CASE_NATURAL,
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
                ];
            };
        });

        artisan($this, 'migrate:fresh');
        $this->loadLaravelMigrations();

        $this->beforeApplicationDestroyed(
            fn () => artisan($this, 'migrate:rollback')
        );
    }

    protected function getPackageProviders($app)
    {
        if (! class_exists('\Workflow\Models\Model')) {
            if (env('DB_CONNECTION') === 'mongodb') {
                class_alias(\Jenssegers\Mongodb\Eloquent\Model::class, '\Workflow\Models\Model');
            } else {
                class_alias(\Illuminate\Database\Eloquent\Model::class, '\Workflow\Models\Model');
            }
        }

        $app['config']->set('database.connections.mongodb', [
            'driver' => 'mongodb',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 27017),
            'database' => env('DB_DATABASE', 'homestead'),
            'username' => env('DB_USERNAME', 'homestead'),
            'password' => env('DB_PASSWORD', 'secret'),
        ]);

        return ['Jenssegers\Mongodb\MongodbServiceProvider', 'Waterline\WaterlineServiceProvider', 'Waterline\WaterlineApplicationServiceProvider'];
    }
}
