<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Tests\laravel\app\View\Components;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;
use Miko\LaravelLatte\Tests\laravel\app\DISomething;

class MyComponent implements IComponent
{
    private array $params;

    public function __construct(DISomething $somethinFromDI)
    {

    }

    public function init(...$params): void
    {
        $this->params = $params;
    }

    public function render(): View|string
    {
        $asString = $this->params['asString'] ?? false;
        return $asString ? 'Render as string' : view('component.my-component', $this->params);
    }
}