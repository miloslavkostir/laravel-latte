<?php

namespace Miko\LaravelLatte\Tests;

use Illuminate\Foundation\Testing\TestCase;

/**
 * Singleton
 */
final class TestEnvConfig
{
    private static ?self $instance = null;

    public bool $tracy;
    public bool $livewire;

    private function __construct()
    {
        $this->defaults();
    }

    public static function get(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function defaults()
    {
        $this->livewire = true;
        $this->tracy = false;
    }
}