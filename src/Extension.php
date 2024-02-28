<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Latte\Extension as LatteExtension;
use Livewire\LivewireManager;

class Extension extends LatteExtension
{
    public function getTags(): array
    {
        $tags = [
            'n:href' => [Nodes\LinkNode::class, 'create'],
            'link' => [Nodes\LinkNode::class, 'create'],
            'n:src' => [Nodes\AssetNode::class, 'create'],
            'asset' => [Nodes\AssetNode::class, 'create'],
            'csrf' => [Nodes\CsrfNode::class, 'create'],
            'method' => [Nodes\MethodNode::class, 'create'],
            'dump' => [Nodes\DumpNode::class, 'create'],
        ];
        if (class_exists(LivewireManager::class, false)) {
            $tags['livewire'] = [Nodes\LivewireNode::class, 'create'];
            $tags['livewireStyles'] = [Nodes\LivewireStylesNode::class, 'create'];
            $tags['livewireScripts'] = [Nodes\LivewireScriptsNode::class, 'create'];
            $tags['livewireScriptConfig'] = [Nodes\LivewireScriptConfigNode::class, 'create'];
        }
        return $tags;
    }
}
