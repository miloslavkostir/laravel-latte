<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Method
{
    public static function generate(string $method, bool $xhtml): string
    {
        self::validateArgument($method);
        if ($xhtml) {
            return '<input type="hidden" name="_method" value="'.$method.'" />';
        } else {
            return '<input type="hidden" name="_method" value="'.$method.'">';
        }
    }

    private static function validateArgument(string $method): bool
    {
        if (in_array(strtoupper($method), ['GET', 'POST'])) {
            throw new \RuntimeException('Do not use GET and POST method via method spoofing. Use the "method" form attribute instead.');
        } elseif (!in_array(strtoupper($method), ['PUT', 'PATCH', 'DELETE'])) {
            throw new \RuntimeException('Only PUT, PATCH and DELETE methods are possible via method spoofing');
        }
        return true;
    }
}
