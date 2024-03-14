<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

use Illuminate\View\View;
use Latte\CompileException;
use Miko\LaravelLatte\IComponent;

class Component
{
    public static function generate(string $name, array $params = []): View|string
    {
        if (! strpos($name, '\\')) {
            $name = self::composeName($name);
        }
        $component = app($name);
        if (! $component instanceof IComponent) {
            throw new CompileException('Component must implement ' . IComponent::class . ' interface');
        }
        /** @var IComponent $component */
        $component->init(...$params);
        return $component->render();
    }

    public static function composeName(string $name): string
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
