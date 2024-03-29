<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Support\Arr;
use Latte\Extension as LatteExtension;

class Extension extends LatteExtension
{
    public function __construct(array $config)
    {
        $xhtml = Arr::get($config, 'xhtml', false);
        Nodes\CsrfNode::$xhtml = $xhtml;
        Nodes\MethodNode::$xhtml = $xhtml;
        Filters\Nl2br::$xhtml = $xhtml;
    }

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
            'nl2br' => [Filters\Nl2br::class, 'handle'],
        ];
    }
}
