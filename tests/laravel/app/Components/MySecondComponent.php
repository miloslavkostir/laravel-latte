<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Tests\laravel\app\Components;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;
use Miko\LaravelLatte\Tests\laravel\app\DISomething;

class MySecondComponent implements IComponent
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
        return view('component.my-second-component', $this->params);
    }
}