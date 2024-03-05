<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

class Csrf
{
    public static function generate(bool $xhtml): string
    {
        if ($xhtml) {
            return '<input type="hidden" name="_token" value="'.csrf_token().'" autocomplete="off" />';
        } else {
            return '<input type="hidden" name="_token" value="'.csrf_token().'" autocomplete="off">';
        }
    }
}
