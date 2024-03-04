<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

if (! class_exists('App\Http\Controllers\TestController')) {
    class_alias(\Miko\LaravelLatte\Tests\laravel\app\TestController::class, 'App\Http\Controllers\TestController');
    class_alias(\Miko\LaravelLatte\Tests\laravel\app\Admin\TestController::class, 'App\Http\Controllers\Admin\TestController');
    class_alias(\Miko\LaravelLatte\Tests\laravel\app\LivewireComponent::class, 'App\Livewire\LivewireComponent');
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();