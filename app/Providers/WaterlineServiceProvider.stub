<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Waterline\WaterlineApplicationServiceProvider;

class WaterlineServiceProvider extends WaterlineApplicationServiceProvider
{
    /**
     * Register the Waterline gate.
     *
     * This gate determines who can access Waterline in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewWaterline', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
