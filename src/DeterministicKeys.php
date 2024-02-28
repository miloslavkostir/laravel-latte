<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

class DeterministicKeys
{
    protected static ?self $instance = null;

    protected array $countersByPathAndPrefix = [];

    protected ?string $path = null;

    public static function generate(string $prefix): string
    {
        $instance = self::getInstance();
        if (! $instance->path) {
            throw new \Exception('Latest compiled path not found.');
        }

        $path = $instance->path;
        $count = $instance->counter($prefix);

        return $prefix . '-' . crc32($path) . '-' . $count;
    }

    protected function counter(string $prefix): int
    {
        if (! isset($this->countersByPathAndPrefix[$prefix][$this->path])) {
            $this->countersByPathAndPrefix[$prefix][$this->path] = 0;
        }

        return $this->countersByPathAndPrefix[$prefix][$this->path]++;
    }

    public static function setPath(string $path): void
    {
        self::getInstance()->path = $path;
    }

    protected static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
