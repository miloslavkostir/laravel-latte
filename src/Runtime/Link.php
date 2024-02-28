<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Runtime;

use Illuminate\Support\Facades\Route;

class Link
{
    public static function generate(string $name, array $parameters = [], bool $absolute = true): string
    {
        $array = explode('@', $name);

        if (count($array) === 1 && $name === 'this' && empty($parameters)) {
            // Just 'this' (without parameters) returns current URL
            return url()->current();
        }

        if (count($array) === 1 && ($current = Route::currentRouteAction()) === null) {
            // 'this' with parameters and the method name must have an action defined
            throw new \RuntimeException('Latte function link(): there is no current route action for "' . $name . '"');
        }

        if (count($array) === 1) {
            // 'this' with parameters or the name of the method - find out the controller and method from the current action
            list($controller, $method) = explode('@', $current);
            if ($name !== 'this') {
                // $name is the name of the method - override
                $method = $name;
            } else {
                // 'this' with parameters - use the current action and add parameters
                $diff = array_diff_key(request()->route()->parameters, $parameters);
                $i = 0;
                foreach ($diff as $n => &$d) {
                    if (isset($parameters[$i])) {
                        $d = $parameters[$i];
                        unset($parameters[$i]);
                    }
                    $i++;
                }
                $parameters = array_merge($diff, $parameters);
            }
        } else {
            // Contoller@method
            $controller = class_exists('App\\Http\\Controllers\\' . $array[0] . 'Controller')
                ? 'App\\Http\\Controllers\\' . $array[0] . 'Controller'
                : 'App\\Http\\Controllers\\' . $array[0];
            $method = $array[1] ?: 'index';
        }

        return action([$controller, $method], $parameters, $absolute);
    }
}
