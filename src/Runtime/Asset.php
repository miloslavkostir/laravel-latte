<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Asset
{
    public static function generate(string $url, string $paramName = 'm'): string
    {
        return $url . "?$paramName=" . filemtime(public_path(ltrim($url, '/')));
    }
}
