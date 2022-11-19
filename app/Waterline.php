<?php

namespace Waterline;

use Closure;
use Illuminate\Support\Facades\File;
use RuntimeException;

class Waterline
{
    public static $authUsing;

    public static function check($request)
    {
        return (static::$authUsing ?: function () {
            return app()->environment('local');
        })($request);
    }

    public static function auth(Closure $callback)
    {
        static::$authUsing = $callback;

        return new static;
    }

    public static function assetsAreCurrent()
    {
        $publishedPath = public_path('vendor/waterline/mix-manifest.json');

        if (! File::exists($publishedPath)) {
            throw new RuntimeException('Waterline assets are not published. Please run: php artisan waterline:publish');
        }

        return File::get($publishedPath) === File::get(__DIR__.'/../public/mix-manifest.json');
    }
}
