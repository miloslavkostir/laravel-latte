<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Csrf
{
    public static function generate(): string
    {
        return (string) \csrf_field();
    }
}
