<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

use Livewire\Mechanisms\FrontendAssets\FrontendAssets;

class Livewire
{
    public static function generate(string $name, array $params = [], ?string $key = null): string
    {
        /** @var \Livewire\LivewireManager $livewire */
        $livewire = app('livewire');

        return $livewire->mount($name, $params, $key);
    }

    public static function styles(array $options = [])
    {
        return FrontendAssets::styles($options);
    }

    public static function scripts(array $options = [])
    {
        return FrontendAssets::scripts($options);
    }

    public static function scriptConfig(array $options = [])
    {
        return FrontendAssets::scriptConfig($options);
    }
}
