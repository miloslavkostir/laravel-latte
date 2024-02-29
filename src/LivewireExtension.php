<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Latte\Extension as LatteExtension;

class LivewireExtension extends LatteExtension
{
    public function getTags(): array
    {
        return [
            'livewire' => [Nodes\LivewireNode::class, 'create'],
            'livewireStyles' => [Nodes\LivewireStylesNode::class, 'create'],
            'livewireScripts' => [Nodes\LivewireScriptsNode::class, 'create'],
            'livewireScriptConfig' => [Nodes\LivewireScriptConfigNode::class, 'create'],
        ];
    }
}
