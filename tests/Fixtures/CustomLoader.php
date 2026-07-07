<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Tests\Fixtures;

use Latte\Loader;

class CustomLoader implements Loader
{
    public function getContent(string $name): string
    {
        return 'Custom loader';
    }

    public function getReferredName(string $name, string $referringName): string
    {
        return $name;
    }

    public function getUniqueId(string $name): string
    {
        return $name;
    }
}
