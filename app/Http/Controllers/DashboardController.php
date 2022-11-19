<?php

namespace Waterline\Http\Controllers;

use Illuminate\Support\Facades\App;

class DashboardController extends Controller
{
    public function index() {
        return view('waterline::layout', [
            'assetsAreCurrent' => true,
            'cssFile' => true ? 'app-dark.css' : 'app.css',
            'waterlineScriptVariables' => [
                'path' => config('waterline.path', 'waterline'),
            ],
            'isDownForMaintenance' => App::isDownForMaintenance(),
        ]);       
    }
}
