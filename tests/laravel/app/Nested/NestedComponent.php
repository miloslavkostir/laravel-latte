<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Tests\laravel\app\Nested;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;
use Miko\LaravelLatte\Tests\laravel\app\DISomething;

class NestedComponent implements IComponent
{

    public function init(...$params): void
    {
    }

    public function render(): View|string
    {
        return 'Nested component';
    }
}