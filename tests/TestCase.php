<?php

namespace Miko\LaravelLatte\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Nette\Utils\FileSystem;

class TestCase extends BaseTestCase
{
    protected const TEMP_DIR = __DIR__ . '/laravel/storage/framework/views';

    public function createApplication()
    {
        $app = require __DIR__ . '/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app['config']->set('app.key', Encrypter::generateKey('dek-apps-laravel-latte'));
        $app['config']->set('debug', true);
        $app['config']->set('session.driver', 'array');
        //$app['config']->set('view.compiled', self::TEMP_DIR);

        return $app;
    }

    protected function getExpected(string $view): string
    {
        return file_get_contents(__DIR__ . '/expected/' . $view . '.html');
    }

    protected function setUp(): void
    {
        FileSystem::delete(self::TEMP_DIR);
        FileSystem::createDir(self::TEMP_DIR);

        parent::setUp();
    }
}