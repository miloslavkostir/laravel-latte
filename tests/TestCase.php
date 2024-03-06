<?php

namespace Miko\LaravelLatte\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Encryption\Encrypter;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use Nette\Utils\Random;
use Tracy\Debugger;

class TestCase extends BaseTestCase
{
    protected const TEMP_DIR = __DIR__ . '/laravel/storage/framework/views';

    public function createApplication()
    {
        $app = require __DIR__ . '/laravel/bootstrap/aliases.php';
        $app = require __DIR__ . '/laravel/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $app['config']->set('app.key', Encrypter::generateKey('dek-apps-laravel-latte'));
        $app['config']->set('app.debug', true);
        $app['config']->set('session.driver', 'array');

        if (TestEnvConfig::get()->tracy) {
            Debugger::enable(Debugger::Development);
        }

        return $app;
    }

    protected function getExpected(string $view): string
    {
        $view = preg_match('#\.[a-z]+$#', $view) ? $view : $view . '.html';
        return file_get_contents(__DIR__ . '/expected/' . $view);
    }

    protected function findCompiled(string $view, string $dir = self::TEMP_DIR): ?string
    {
        $view = str_replace(['.','/'], '-', $view);
        $finder = Finder::findFiles("*$view.latte--*.php")->in($dir);
        $files = $finder->collect();
        return $files[0] ?? null;
    }

    protected function readDirectory(string $dir = self::TEMP_DIR): array
    {
        $dir = dir(self::TEMP_DIR);
        $files = [];
        while ($file = $dir->read()) {
            if ($file === '.' || $file === '..') continue;
            $files[] = $file;
        }
        return $files;
    }

    /**
     * It is necessary to have some views many times, because after the compilation of the first template,
     * the same view would not be compiled a second time, even if the templates are deleted before each test.
     * It is probably because the template file is deleted but template class is still included - class_exists()
     * returns true in Latte\Engine::createTemplate()
     */
    protected function assertUniqueView(string $view, callable $assertions): void
    {
        // copy file for new compiling
        $suffix = Random::generate();
        $view = str_replace('.', '/', $view);
        $orig = resource_path('/views/' . $view . '.latte');
        $copy = resource_path('/views/' . $view . '/' . $suffix . '.latte');
        FileSystem::copy($orig, $copy);

        try {
            // assert
            $assertions($view . '/' . $suffix);
        } finally {
            // clean the mess
            FileSystem::delete(dirname($copy));
        }
    }

    protected function setUp(): void
    {
        FileSystem::delete(self::TEMP_DIR);
        FileSystem::createDir(self::TEMP_DIR);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        TestEnvConfig::get()->defaults();
    }
}