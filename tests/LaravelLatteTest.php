<?php

namespace Miko\LaravelLatte\Tests;

use Nette\Utils\Finder;
use Ssddanbrown\AssertHtml\HtmlTest;
use Symfony\Component\VarDumper\VarDumper;

class LaravelLatteTest extends TestCase
{
    public function test_latte_implemented(): void
    {
        $output = view('implemented', ['foo' => 'Bar'])->render();

        $expected = $this->getExpected('implemented');

        $this->assertEquals($expected, $output);
    }

    public function test_csrf_tag(): void
    {
        $output = view('csrf-tag')->render();

        $expected = $this->getExpected('csrf-tag');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag(): void
    {
        $output = view('method-tag', ['method' => 'PUT'])->render();

        $expected = $this->getExpected('method-tag');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag_without_parameter(): void
    {
        $this->expectException(\Latte\CompileException::class);
        $this->expectExceptionMessage('Missing arguments in {method}');

        view('method-tag-without-parameter')->render();
    }

    public function test_method_tag_unsupported_method(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Only PUT, PATCH and DELETE methods are possible via method spoofing');

        view('method-tag', ['method' => 'FOO'])->render();
    }

    public function test_method_tag_get_method(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Do not use GET and POST method via method spoofing. Use the "method" form attribute instead.');

        view('method-tag', ['method' => 'GET'])->render();
    }

    public function test_asset_tag(): void
    {
        $time = filemtime(public_path('style.css'));

        $output = view('asset-tag')->render();

        $this->assertEquals('/style.css?m=' . $time, $output);
    }

    public function test_asset_tag_without_parameter(): void
    {
        $this->expectException(\Latte\CompileException::class);
        $this->expectExceptionMessage('Missing arguments in {asset}');

        view('asset-tag-without-parameter')->render();
    }

    public function test_n_src_tag(): void
    {
        $time = filemtime(public_path('script.js'));

        $output = view('n-src-tag')->render();

        $this->assertEquals('<script src="/script.js?m=' . $time . '" async></script>', $output);
    }

    public function test_link_tag(): void
    {
        $response = $this->get('/test/link/987');

        $expected = $this->getExpected('link-tag');

        $response->assertStatus(200);
        $response->assertContent($expected);
    }

    public function test_n_href_tag(): void
    {
        $response = $this->get('/test/n-href/987');

        $expected = $this->getExpected('n-href-tag');

        $response->assertStatus(200);
        $response->assertContent($expected);
    }

    public function test_livewire_tag(): void
    {
        $html = new HtmlTest(view('livewire-tag')->render());

        $html->assertElementContains('h1', 'My component');
        $html->assertElementExists('div[wire\:snapshot]');
        $html->assertElementExists('div[wire\:effects]');
        $html->assertElementExists('div[wire\:id]');
        $html->assertElementContains('div', 'Variable lorem from my component is ipsum');
    }

    public function test_multiple_livewire_tag(): void
    {
        $html = new HtmlTest(view('livewire-tags')->render());

        $html->assertElementContains('h1', 'My component multiple');
        $html->assertElementCount('div[wire\:snapshot]', 5);
        $html->assertElementCount('div[wire\:effects]', 5);
        $html->assertElementCount('div[wire\:id]', 5);

        // Check correctly generated keys
        $finder = Finder::findFiles('resources-views-livewire-tags.latte--*.php')->in(self::TEMP_DIR);
        $files = $finder->collect();
        $pattern = function (string|int $key, string|int $line) {
            if (is_int($key)) {
                $key = 'lw-'.crc32(realpath(resource_path('views/livewire-tags.latte'))).'-'.$key;
            }
            return 'echo ' . preg_quote('\Miko\LaravelLatte\Runtime\Livewire') . "::generate\('my-component', \[\], '$key'\) \/\* line $line \*\/";
        };

        $this->assertMatchesRegularExpression('#'.$pattern(0, 2).'#', file_get_contents($files[0]));
        $this->assertMatchesRegularExpression('#'.$pattern(1, 3).'#', file_get_contents($files[0]));
        $this->assertMatchesRegularExpression('#'.$pattern(2, 4).'#', file_get_contents($files[0]));
        $this->assertDoesNotMatchRegularExpression('#'.$pattern(3, '.').'#', file_get_contents($files[0]));
        $this->assertMatchesRegularExpression('#'.$pattern('lorem', 5).'#', file_get_contents($files[0]));
        $this->assertMatchesRegularExpression('#'.$pattern('ipsum', 6).'#', file_get_contents($files[0]));
    }

    public function test_livewire_styles_tag(): void
    {
        $html = new HtmlTest(view('livewire-styles-tag')->render());
        $html->assertElementExists('html head style');
    }

    public function test_livewire_scripts_tag(): void
    {
        $html = new HtmlTest(view('livewire-scripts-tag')->render());

        $html->assertElementExists('html body script');
        $html->assertElementExists('html body script[src*=livewire]');
        $html->assertElementExists('html body script[data-csrf]');
        $html->assertElementExists('html body script[data-update-uri]');
        $html->assertElementExists('html body script[data-navigate-once]');
    }

    public function test_livewire_script_config_tag(): void
    {
        $html = new HtmlTest(view('livewire-script-config-tag')->render());

        $html->assertElementExists('html body script');
        $html->assertElementExists('html body script[data-navigate-once]');
        $html->assertElementContains('html body script', 'window.livewireScriptConfig = {"csrf":null,"uri":"\/livewire\/update","progressBar":"","nonce":""};');
    }

    public function test_dump_tag(): void
    {
        VarDumper::setHandler(function($var, $label){
            echo '<pre>' . var_export($var, true) . '</pre>';
        });

        $output = view('dump-tag')->render();

        $this->assertEquals("<pre>'Dumped variable'</pre>", $output);
    }
}