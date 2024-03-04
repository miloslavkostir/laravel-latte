<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

interface IComponent
{
    public function init(...$params): void;

    public function render(): \Illuminate\View\View|string;
}