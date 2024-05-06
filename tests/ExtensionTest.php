<?php

namespace Miko\LaravelLatte\Tests;

use Symfony\Component\VarDumper\VarDumper;

class ExtensionTest extends TestCase
{
    public function test_csrf_tag_html(): void
    {
        $output = view('extension/csrf-tag')->render();

        $expected = $this->getExpected('csrf-tag.html');

        $this->assertEquals($expected, $output);
    }

    public function test_csrf_tag_xhtml(): void
    {
        $this->app['config']->set('latte.xhtml', true);

        $output = view('extension/csrf-tag-x')->render();

        $expected = $this->getExpected('csrf-tag.xhtml');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag_html(): void
    {
        $output = view('extension/method-tag', ['method' => 'PUT'])->render();

        $expected = $this->getExpected('method-tag.html');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag_xhtml(): void
    {
        $this->app['config']->set('latte.xhtml', true);

        $output = view('extension/method-tag-x', ['method' => 'PUT'])->render();

        $expected = $this->getExpected('method-tag.xhtml');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag_without_parameter(): void
    {
        $this->expectException(\Latte\CompileException::class);
        $this->expectExceptionMessage('Missing arguments in {method}');

        view('extension/method-tag-without-parameter')->render();
    }

    public function test_method_tag_null_parameter(): void
    {
        $output = view('extension/method-tag-empty-parameter', [
            'var1' => '',
            'var2' => null,
            'var3' => false,
            'var4' => 0,
        ])->render();

        $this->assertEquals(str_repeat(PHP_EOL, 7), $output);
    }

    public function test_asset_tag(): void
    {
        $time1 = filemtime(public_path('style.css'));
        $time2 = filemtime(public_path('script.js'));

        $output = view('extension/asset-tag', ['src' => '/script.js'])->render();

        $expected = <<<HTML
        /style.css?m=$time1
        /script.js?m=$time2
        HTML;

        $this->assertEquals($expected, $output);
    }

    public function test_asset_tag_without_parameter(): void
    {
        $this->expectException(\Latte\CompileException::class);
        $this->expectExceptionMessage('Missing arguments in {asset}');

        view('extension/asset-tag-without-parameter')->render();
    }

    public function test_n_src_tag(): void
    {
        $time1 = filemtime(public_path('style.css'));
        $time2 = filemtime(public_path('script.js'));

        $output = view('extension/n-src-tag', ['src' => '/script.js'])->render();

        $expected = <<<HTML
        <script src="/script.js?m=$time1" async></script>
        <script src="/script.js?m=$time2" async></script>
        HTML;

        $this->assertEquals($expected, $output);
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

    public function test_dump_tag_symfony(): void
    {
        // Refresh app without tracy
        TestEnvConfig::get()->tracy = false;
        $this->refreshApplication();

        VarDumper::setHandler(function($var, $label){
            echo '<pre>' . var_export($var, true) . '</pre>';
        });

        $output = view('extension/dump-tag')->render();

        $this->assertEquals("<pre>'Dumped variable'</pre>", $output);
    }

    public function test_dump_tag_tracy(): void
    {
        // Refresh app with tracy
        TestEnvConfig::get()->tracy = true;
        $this->refreshApplication();

        VarDumper::setHandler(function($var, $label){
            echo '<pre>' . var_export($var, true) . '</pre>';
        });

        $output = view('extension/dump-tag')->render();

        $this->assertEquals("", $output);
    }

    public function test_component_tag(): void
    {
        $output = view('component/x-tag', ['name' => 'my-component'])->render();

        $expected = <<<HTML
        <h1>My component </h1>
        <h1>My component </h1>
        <h1>My component bar</h1>
        <h1>My component bar</h1>
        <h1>My component bar</h1>
        Render as string
        Nested component
        HTML;

        $this->assertEquals($expected, $output);
    }

    public function test_component_tag_another_namespace(): void
    {
        $this->app['config']->set('latte.components_namespace', 'App\\Components');

        $output = view('component/x-tag-config-namespace', ['name' => 'my-second-component'])->render();

        $expected = <<<HTML
        <h1>My second component </h1>
        <h1>My second component </h1>
        <h1>My second component bar</h1>
        HTML;

        $this->assertEquals($expected, $output);
    }

    public function test_nl2br_filter_html(): void
    {
        $text = <<<TEXT
            Lorem
            ipsum
            dolor sit
            TEXT;

        $output = view('extension/nl2br-filter', ['foo' => $text])->render();

        $expected = $this->getExpected('nl2br-filter.html');

        $this->assertEquals($expected, $output);
    }
}