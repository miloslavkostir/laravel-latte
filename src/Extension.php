<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Latte\Extension as LatteExtension;
use Miko\LaravelLatte\Filters\Nl2br;

class Extension extends LatteExtension
{
    public function getTags(): array
    {
        return [
            'n:href' => [Nodes\LinkNode::class, 'create'],
            'link' => [Nodes\LinkNode::class, 'create'],
            'n:src' => [Nodes\AssetNode::class, 'create'],
            'asset' => [Nodes\AssetNode::class, 'create'],
            'csrf' => [Nodes\CsrfNode::class, 'create'],
            'method' => [Nodes\MethodNode::class, 'create'],
            'dump' => [Nodes\DumpNode::class, 'create'],
            'x' => [Nodes\ComponentNode::class, 'create'],
        ];
    }

    public function getFilters(): array
    {
        return [
            'nl2br' => [Nl2br::class, 'handle'],
        ];
    }
}
