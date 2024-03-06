<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Support\Facades\App;
use Latte\Engine;
use Latte\Extension as LatteExtension;

class TranslationExtension extends LatteExtension
{
    public function __construct(bool $autoRefresh)
    {
        Nodes\TranslationNode::$autoRefresh = $autoRefresh;
    }

    public function getTags(): array
    {
        return [
            '_' => [Nodes\TranslationNode::class, 'create'],
        ];
    }

    public function getCacheKey(Engine $engine): mixed
    {
        return App::getLocale();
    }
}
