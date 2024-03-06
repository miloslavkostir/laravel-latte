<?php

namespace Miko\LaravelLatte\Runtime;

class Translator
{
    /**
     * Latte expects only one translate() method, while Laravel provides
     * two methods trans() and trans_choice().
     * This method merges function of both.
     *
     * @param string $key
     * @param int|array $number Int for trans_choice(), array as $replace for trans()
     * @param array|string $replace Array for trans_choice(), string as $locale for trans()
     * @param ?string $locale
     */
    public static function translate(string $key, int|array $number = [], array|string $replace = [], ?string $locale = null): string
    {
        if (is_string($replace)) {
            $locale = $replace;
            $replace = [];
        }
        if (is_array($number)) {
            $replace = $number;
            $number = [];
        }

        if ($number !== []) {
            return trans_choice($key, $number, $replace, $locale);
        } else {
            return trans($key, $replace, $locale);
        }
    }
}