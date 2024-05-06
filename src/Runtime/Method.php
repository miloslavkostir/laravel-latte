<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Method
{
    public static function generate(?string $method, bool $xhtml): string
    {
        if (!$method || in_array(strtolower($method), ['null', 'false'])) {
            return '';
        } elseif ($xhtml) {
            return '<input type="hidden" name="_method" value="'.$method.'" />';
        } else {
            return '<input type="hidden" name="_method" value="'.$method.'">';
        }
    }
}
