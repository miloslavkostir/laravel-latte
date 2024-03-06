<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Filters;

use Latte\Runtime\Html;
use Tracy\Debugger;

class Nl2br
{
    public static bool $xhtml = false;

    public static function handle(string $text, bool $xhtml = null): Html
    {
        return new Html(nl2br($text, $xhtml ?? self::$xhtml));
    }
}