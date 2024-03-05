<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Filters;

use Latte\Runtime\Html;
use Tracy\Debugger;

class Nl2br
{
    private static ?bool $xhtml = null;

    public static function handle(string $text, bool $xhtml = null): Html
    {
        // read from the config only for first time
        if ($xhtml === null && self::$xhtml === null) {
            self::$xhtml = $xhtml = config('latte.xhtml', false);
        }
        return new Html(nl2br($text, $xhtml ?? self::$xhtml));
    }
}