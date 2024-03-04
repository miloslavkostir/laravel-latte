<?php

$aliases = [
    \Miko\LaravelLatte\Tests\laravel\app\TestController::class => 'App\Http\Controllers\TestController',
    \Miko\LaravelLatte\Tests\laravel\app\Admin\TestController::class => 'App\Http\Controllers\Admin\TestController',
    \Miko\LaravelLatte\Tests\laravel\app\LivewireComponent::class => 'App\Livewire\LivewireComponent',
    \Miko\LaravelLatte\Tests\laravel\app\MyComponent::class => 'App\View\Components\MyComponent',
    \Miko\LaravelLatte\Tests\laravel\app\Nested\NestedComponent::class => 'App\View\Components\Nested\NestedComponent',
];

foreach ($aliases as $class => $alias) {
    if (! class_exists($alias)) {
        class_alias($class, $alias);
    }
}