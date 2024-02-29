<?php

/**
 * There are some views three times, because after the compilation of the first template,
 * the same view would not be compiled a second time, even if the templates are deleted before each test.
 * It is probably because the template file is deleted but template class is still included - class_exists()
 * returns true in Latte\Engine::createTemplate()
 */

namespace Miko\LaravelLatte\Tests;

use Latte\CompileException;
use Latte\RuntimeException;
use Nette\Utils\Finder;

class ConfigTest extends TestCase
{

    public function test_not_configured_layout(): void
    {
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

    public function test_not_configured_strict_types(): void
    {
        view('config/strict-types-1')->render();

        $finder = Finder::findFiles('views-config-strict-types-1.latte--*.php')->in(self::TEMP_DIR);
        $files = $finder->collect();

        $this->assertDoesNotMatchRegularExpression('#declare\(strict_types=1\)#', file_get_contents($files[0]));
    }

    public function test_configured_strict_types_false(): void
    {
        $this->app['config']->set('latte.strict_types', false);

        view('config/strict-types-2')->render();

        $finder = Finder::findFiles('views-config-strict-types-2.latte--*.php')->in(self::TEMP_DIR);
        $files = $finder->collect();

        $this->assertDoesNotMatchRegularExpression('#declare\(strict_types=1\)#', file_get_contents($files[0]));
    }

    public function test_configured_strict_types_true(): void
    {
        $this->app['config']->set('latte.strict_types', true);

        view('config/strict-types-3')->render();

        $finder = Finder::findFiles('views-config-strict-types-3.latte--*.php')->in(self::TEMP_DIR);
        $files = $finder->collect();

        $this->assertMatchesRegularExpression('#declare\(strict_types=1\)#', file_get_contents($files[0]));
    }

    public function test_not_configured_strict_parsing(): void
    {
        $output = view('config/unclosed-element-1')->render();

        $expected = <<<HTML
        <h1>Template with unclosed element 1</h1>
        <div>
        HTML;

        $this->assertEquals($expected, $output);
    }

    public function test_configured_strict_parsíng_false(): void
    {
        $output = view('config/unclosed-element-2')->render();

        $expected = <<<HTML
        <h1>Template with unclosed element 2</h1>
        <div>
        HTML;

        $this->assertEquals($expected, $output);
    }

    public function test_configured_strict_parsíng_true(): void
    {
        $this->expectException(CompileException::class);
        $this->expectExceptionMessage('Unexpected end, expecting </div> for element started on line 2 at column 1');

        $this->app['config']->set('latte.strict_parsing', true);

        view('config/unclosed-element-3')->render();
    }
}