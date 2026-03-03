<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Method
{
    public static function generate(mixed $method, bool $xhtml): string
    {
        $method = (string) $method;
        if (!$method || in_array(strtolower($method), ['', 'null', 'false', '0'])) {
            return '';
        } elseif ($xhtml) {
            return '<input type="hidden" name="_method" value="'.$method.'" />';
        } else {
            return '<input type="hidden" name="_method" value="'.$method.'">';
        }
    }
}
