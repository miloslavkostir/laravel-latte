<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

use Miko\LaravelLatte\DeterministicKeys;

class Livewire
{
    public static function generate(string $name, array $params = []): string
    {
        $rt = new self();
        $key = $rt->findKey($params);

        /** @var \Livewire\LivewireManager $livewire */
        $livewire = app('livewire');

        return $livewire->mount($name, $params, $key);
    }

    private function findKey(array &$params): string
    {
        foreach ($params as $index => $param) {
            if ($index === 'key') {
                $key = $param;
                unset($params[$index]);
                return $key;
            }
        }
        return DeterministicKeys::generate('lw');
    }

    public static function styles(array $options = [])
    {
        return \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles($options);
    }

    public static function scripts(array $options = [])
    {
        return \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts($options);
    }

    public static function scriptConfig(array $options = [])
    {
        return \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scriptConfig($options);
    }
}
