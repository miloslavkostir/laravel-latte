<?php

namespace Miko\LaravelLatte\Tests;

use Latte\CompileException;
use Nette\Utils\Finder;
use Ssddanbrown\AssertHtml\HtmlTest;
use Symfony\Component\VarDumper\VarDumper;

class LivewireExtensionTest extends TestCase
{
    public function test_livewire_tag(): void
    {
        $html = new HtmlTest(view('livewire/livewire-tag')->render());

        $html->assertElementContains('h1', 'My component');
        $html->assertElementExists('div[wire\:snapshot]');
        $html->assertElementExists('div[wire\:effects]');
        $html->assertElementExists('div[wire\:id]');
        $html->assertElementContains('div', 'Variable lorem from my component is ipsum');
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