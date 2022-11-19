<?php

namespace Waterline;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class WaterlineApplicationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->authorization();
    }

    protected function authorization()
    {
        $this->gate();

        Waterline::auth(function ($request) {
            return Gate::check('viewWaterline', [$request->user()]) || app()->environment('local');
        });
    }

    protected function gate()
    {
        Gate::define('viewWaterline', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    public function register()
    {
        //
    }
}
