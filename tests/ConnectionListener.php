<?php

use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestListenerDefaultImplementation;
use PHPUnit\Framework\TestSuite;

class ConnectionListener implements TestListener
{
    use TestListenerDefaultImplementation;

    public function startTestSuite(TestSuite $suite): void
    {
        switch ($suite->getName()) {
            case 'Feature':
                putenv('DB_CONNECTION=mysql');
                putenv('DB_HOST=mysql');
                putenv('DB_PORT=3306');
                putenv('DB_DATABASE=testing');
                putenv('DB_USERNAME=testing');
                putenv('DB_PASSWORD=password');
                break;
                // putenv('DB_CONNECTION=sqlite');
                // putenv('DB_DATABASE=:memory:');
                // break;
        }
    }
}
