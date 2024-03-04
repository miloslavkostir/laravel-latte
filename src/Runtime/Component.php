<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

use Illuminate\View\View;
use Miko\LaravelLatte\IComponent;

class Component
{
    public static function generate(string $name, array $params = []): View|string
    {
        $name = self::composeName($name);

        /** @var IComponent $renderable */
        $renderable = app($name);
        $renderable->init(...$params);
        return $renderable->render();
    }

    private static function composeName(string $name): string
    {
        $splitByDash = preg_split('#-#', $name);
        if (count($splitByDash) > 1) {
            array_walk($splitByDash, fn (&$val) => $val = ucfirst($val));
            $name = implode('', $splitByDash);
        }

        $splitByDot = explode('.', $name);
        if (count($splitByDot) > 1) {
            array_walk($splitByDot, fn (&$val) => $val = ucfirst($val));
            $name = implode('\\', $splitByDot);
        }

        $ns = rtrim(config('latte.components_namespace'), '\\');
        return $ns . '\\' . $name;
    }
}
