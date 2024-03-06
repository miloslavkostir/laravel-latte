<?php

/**
 * There are some views many times, because after the compilation of the first template,
 * the same view would not be compiled a second time, even if the templates are deleted before each test.
 * It is probably because the template file is deleted but template class is still included - class_exists()
 * returns true in Latte\Engine::createTemplate()
 */

namespace Miko\LaravelLatte\Tests;

use Latte\CompileException;
use Latte\RuntimeException;
use Nette\Utils\FileSystem;

class ConfigTest extends TestCase
{
    // latte.compiled¨

    public function test_not_configured_compiled(): void
    {
        // No 'latte.compiled' config
        $this->app['config']->set('view.compiled', self::TEMP_DIR);

        $this->assertUniqueView('config/compiled', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView, self::TEMP_DIR);

            $this->assertNotNull($file);
            $this->assertFileExists($file);
        });
    }

    public function test_configured_compiled_null(): void
    {
        $this->app['config']->set('latte.compiled', null);
        $this->app['config']->set('view.compiled', self::TEMP_DIR);

        $this->assertUniqueView('config/compiled', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView, self::TEMP_DIR);

            $this->assertNotNull($file);
            $this->assertFileExists($file);
        });
    }

    public function test_configured_compiled_null_view_compiled_null(): void
    {
        $this->app['config']->set('latte.compiled', null);
        $this->app['config']->set('view.compiled', null);

        $this->assertUniqueView('config/compiled', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView, self::TEMP_DIR);
            $dir = $this->readDirectory(self::TEMP_DIR);

            $this->assertNull($file);
            $this->assertCount(0, $dir);
        });
    }

    public function test_configured_compiled_false(): void
    {
        $this->app['config']->set('latte.compiled', false);
        $this->app['config']->set('view.compiled', self::TEMP_DIR);

        $this->assertUniqueView('config/compiled', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView, self::TEMP_DIR);
            $dir = $this->readDirectory(self::TEMP_DIR);

            $this->assertNull($file);
            $this->assertCount(0, $dir);
        });
    }

    public function test_configured_compiled(): void
    {
        $tempFile = storage_path('/latte');
        FileSystem::delete($tempFile);
        FileSystem::createDir($tempFile);

        $this->app['config']->set('latte.compiled', $tempFile);
        $this->app['config']->set('view.compiled', self::TEMP_DIR);

        $this->assertUniqueView('config/compiled', function (string $newView) use($tempFile) {
            view($newView)->render();

            $file1 = $this->findCompiled($newView, $tempFile);
            $file2 = $this->findCompiled($newView, self::TEMP_DIR);
            $dir2 = $this->readDirectory(self::TEMP_DIR);

            $this->assertNotNull($file1);
            $this->assertFileExists($file1);

            $this->assertNull($file2);
            $this->assertCount(0, $dir2);
        });
    }

    // latte.layout

    public function test_not_configured_layout(): void
    {
        $output1 = view('config/block')->render();
        $output2 = view('config/block-layout-none')->render();
        $output3 = view('config/nested/block')->render();

        $this->assertEquals('<h1>Block</h1>', $output1);
        $this->assertEquals('<h1>Block with layout none</h1>', $output2);
        $this->assertEquals('<h1>Nested block</h1>', $output3);
    }

    public function test_configured_layout_null(): void
    {
        $this->app['config']->set('latte.layout', null);

        $output1 = view('config/block')->render();
        $output2 = view('config/block-layout-none')->render();
        $output3 = view('config/nested/block')->render();

        $this->assertEquals('<h1>Block</h1>', $output1);
        $this->assertEquals('<h1>Block with layout none</h1>', $output2);
        $this->assertEquals('<h1>Nested block</h1>', $output3);
    }

    public function test_configured_layout(): void
    {
        $this->app['config']->set('latte.layout', resource_path('views/config/base_layout.latte'));

        $output1 = $this->view('config/block');
        $output2 = view('config/block-layout-none')->render();
        $output3 = $this->view('config/nested/block');

        $output1->assertSee('<title>Base layout</title>', false);
        $output1->assertSee('<h1>Block</h1>', false);
        $this->assertEquals('<h1>Block with layout none</h1>', $output2);
        $output3->assertSee('<title>Base layout</title>', false);
        $output3->assertSee('<h1>Nested block</h1>', false);
    }

    public function test_configured_layout_relative(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing template file');

        $this->app['config']->set('latte.layout', 'base_layout.latte');

        $this->view('config/nested/block');
    }

    // latte.auto_refresh

    private function assertAutoRefresh(bool $expected): void
    {
        /** @var \Latte\Engine $latte */
        $latte = $this->app->get('Latte\Engine');
        $reflection = new \ReflectionClass($latte);
        $autoRefresh = $reflection->getProperty('autoRefresh')->getValue($latte);
        $this->assertTrue($autoRefresh === $expected,
            'Latte\Engine::$autoRefresh is not expected ' . ($expected ? 'true' : 'false'));
    }

    private function assertPrecompiledTranslations(bool $expectedPrecompiled): void
    {
        $this->assertUniqueView('config/auto-refresh', function (string $newView) use($expectedPrecompiled) {
            view($newView)->render();

            $file = $this->findCompiled($newView);

            $this->assertNotNull($file);
            if ($expectedPrecompiled) {
                $this->assertDoesNotMatchRegularExpression("#Translator::translate\('messages.welcome',#", file_get_contents($file));
                $this->assertMatchesRegularExpression("#Welcome to our application!#", file_get_contents($file));
            } else {
                $this->assertMatchesRegularExpression("#Translator::translate\('messages.welcome',#", file_get_contents($file));
                $this->assertDoesNotMatchRegularExpression("#Welcome to our application!#", file_get_contents($file));
            }
        });
    }

    public function test_not_configured_auto_refresh_debug_true(): void
    {
        // No 'latte.auto_refresh' config
        $this->app['config']->set('app.debug', true);

        $this->assertAutoRefresh(true);
        $this->assertPrecompiledTranslations(false);
    }

    public function test_not_configured_auto_refresh_debug_false(): void
    {
        // No 'latte.auto_refresh' config
        $this->app['config']->set('app.debug', false);

        $this->assertAutoRefresh(false);
        $this->assertPrecompiledTranslations(true);
    }

    public function test_configured_auto_refresh_null_debug_true(): void
    {
        $this->app['config']->set('latte.auto_refresh', null);
        $this->app['config']->set('app.debug', true);

        $this->assertAutoRefresh(true);
        $this->assertPrecompiledTranslations(false);
    }

    public function test_configured_auto_refresh_null_debug_false(): void
    {
        $this->app['config']->set('latte.auto_refresh', null);
        $this->app['config']->set('app.debug', false);

        $this->assertAutoRefresh(false);
        $this->assertPrecompiledTranslations(true);
    }

    public function test_configured_auto_refresh_false(): void
    {
        $this->app['config']->set('latte.auto_refresh', false);
        $this->app['config']->set('app.debug', true);

        $this->assertAutoRefresh(false);
        $this->assertPrecompiledTranslations(true);
    }

    public function test_configured_auto_refresh_true(): void
    {
        $this->app['config']->set('latte.auto_refresh', true);
        $this->app['config']->set('app.debug', false);

        $this->assertAutoRefresh(true);
        $this->assertPrecompiledTranslations(false);
    }

    // latte.strict_parsing

    public function test_not_configured_strict_parsing(): void
    {
        $this->assertUniqueView('config/unclosed-element', function (string $newView) {
            $output = view($newView)->render();

            $expected = <<<HTML
            <h1>Template with unclosed element</h1>
            <div>
            HTML;

            $this->assertEquals($expected, $output);
        });
    }

    public function test_configured_strict_parsing_null(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Latte\Engine::setStrictParsing(): Argument #1 ($on) must be of type bool, null given');

        $this->app['config']->set('latte.strict_parsing', null);

        $this->assertUniqueView('config/unclosed-element', function (string $newView) {
            view($newView)->render();
        });
    }

    public function test_configured_strict_parsíng_false(): void
    {
        $this->app['config']->set('latte.strict_parsing', false);

        $this->assertUniqueView('config/unclosed-element', function (string $newView) {
            $output = view($newView)->render();

            $expected = <<<HTML
            <h1>Template with unclosed element</h1>
            <div>
            HTML;

            $this->assertEquals($expected, $output);
        });
    }

    public function test_configured_strict_parsing_true(): void
    {
        $this->expectException(CompileException::class);
        $this->expectExceptionMessage('Unexpected end, expecting </div> for element started on line 2 at column 1');

        $this->app['config']->set('latte.strict_parsing', true);

        $this->assertUniqueView('config/unclosed-element', function (string $newView) {
            view($newView)->render();
        });
    }

    // latte.strict_types

    public function test_not_configured_strict_types(): void
    {
        $this->assertUniqueView('config/strict-types', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView);

            $this->assertDoesNotMatchRegularExpression('#declare\(strict_types=1\)#', file_get_contents($file));
        });
    }

    public function test_configured_strict_types_null(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Latte\Engine::setStrictTypes(): Argument #1 ($on) must be of type bool, null given');

        $this->app['config']->set('latte.strict_types', null);

        $this->assertUniqueView('config/strict-types', function (string $newView) {
            view($newView)->render();
        });
    }

    public function test_configured_strict_types_false(): void
    {
        $this->app['config']->set('latte.strict_types', false);

        $this->assertUniqueView('config/strict-types', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView);

            $this->assertDoesNotMatchRegularExpression('#declare\(strict_types=1\)#', file_get_contents($file));
        });
    }

    public function test_configured_strict_types_true(): void
    {
        $this->app['config']->set('latte.strict_types', true);

        $this->assertUniqueView('config/strict-types', function (string $newView) {
            view($newView)->render();

            $file = $this->findCompiled($newView);

            $this->assertMatchesRegularExpression('#declare\(strict_types=1\)#', file_get_contents($file));
        });
    }
}