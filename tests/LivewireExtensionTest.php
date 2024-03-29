<?php

namespace Miko\LaravelLatte\Tests;

use Latte\CompileException;
use Ssddanbrown\AssertHtml\HtmlTest;

class LivewireExtensionTest extends TestCase
{
    public function test_livewire_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-tag', ['name' => 'livewire-component'])->render());

        $html->assertElementContains('h1', 'Livewire component');
        $html->assertElementcount('div[wire\:snapshot]', 5);
        $html->assertElementcount('div[wire\:effects]', 5);
        $html->assertElementcount('div[wire\:id]', 5);
        $html->assertElementContains('div', 'Variable lorem from livewire component is ipsum');
    }

    public function test_livewire_tag_unavailable(): void
    {
        // Refresh app without livewire
        TestEnvConfig::get()->livewire = false;
        $this->refreshApplication();

        $this->expectException(CompileException::class);
        $this->expectExceptionMessage('Unexpected tag {livewire}');

        view('livewire/livewire-tag', ['name' => 'livewire-component'])->render();
    }

    public function test_multiple_livewire_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-tags')->render());

        $html->assertElementContains('h1', 'Livewire component multiple');
        $html->assertElementCount('div[wire\:snapshot]', 5);
        $html->assertElementCount('div[wire\:effects]', 5);
        $html->assertElementCount('div[wire\:id]', 5);

        // Check correctly generated keys
        $file = $this->findCompiled('livewire/livewire-tags');
        $pattern = function (string|int $key, string|int $line) {
            if (is_int($key)) {
                $key = 'lw-'.crc32(realpath(resource_path('views/livewire/livewire-tags.latte'))).'-'.$key;
            }
            return 'echo ' . preg_quote('\Miko\LaravelLatte\Runtime\Livewire') . "::generate\('livewire-component', \[\], '$key'\) \/\* line $line \*\/";
        };

        $this->assertMatchesRegularExpression('#'.$pattern(0, 2).'#', file_get_contents($file));
        $this->assertMatchesRegularExpression('#'.$pattern(1, 3).'#', file_get_contents($file));
        $this->assertMatchesRegularExpression('#'.$pattern(2, 4).'#', file_get_contents($file));
        $this->assertDoesNotMatchRegularExpression('#'.$pattern(3, '.').'#', file_get_contents($file));
        $this->assertMatchesRegularExpression('#'.$pattern('lorem', 5).'#', file_get_contents($file));
        $this->assertMatchesRegularExpression('#'.$pattern('ipsum', 6).'#', file_get_contents($file));
    }

    public function test_livewire_styles_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-styles-tag')->render());
        $html->assertElementExists('html head style');
    }

    public function test_livewire_scripts_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-scripts-tag')->render());

        $html->assertElementExists('html body script');
        $html->assertElementExists('html body script[src*=livewire]');
        $html->assertElementExists('html body script[data-csrf]');
        $html->assertElementExists('html body script[data-update-uri]');
        $html->assertElementExists('html body script[data-navigate-once]');
    }

    public function test_livewire_script_config_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-script-config-tag')->render());

        $html->assertElementExists('html body script');
        $html->assertElementExists('html body script[data-navigate-once]');
        $html->assertElementContains('html body script', 'window.livewireScriptConfig = {"csrf":null,"uri":"\/livewire\/update","progressBar":"","nonce":""};');
    }
}