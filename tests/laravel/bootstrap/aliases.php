<?php

$aliases = [
     'App\Http\Controllers\TestController' => \Miko\LaravelLatte\Tests\laravel\app\Http\Controllers\TestController::class,
     'App\Http\Controllers\Admin\TestController' => \Miko\LaravelLatte\Tests\laravel\app\Http\Controllers\Admin\TestController::class,
     'App\Livewire\LivewireComponent' => \Miko\LaravelLatte\Tests\laravel\app\Livewire\LivewireComponent::class,
     'App\View\Components\MyComponent' => \Miko\LaravelLatte\Tests\laravel\app\View\Components\MyComponent::class,
     'App\View\Components\Nested\NestedComponent' => \Miko\LaravelLatte\Tests\laravel\app\View\Components\Nested\NestedComponent::class,
     'App\Components\MySecondComponent' => \Miko\LaravelLatte\Tests\laravel\app\Components\MySecondComponent::class,
];

foreach ($aliases as $alias => $class) {
    if (! class_exists($alias)) {
        class_alias($class, $alias);
    }
}