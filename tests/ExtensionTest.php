<?php

namespace Miko\LaravelLatte\Tests;

use Symfony\Component\VarDumper\VarDumper;

class ExtensionTest extends TestCase
{
    public function test_csrf_tag(): void
    {
        $output = view('extension/csrf-tag')->render();

        $expected = $this->getExpected('csrf-tag');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag(): void
    {
        $output = view('extension/method-tag', ['method' => 'PUT'])->render();

        $expected = $this->getExpected('method-tag');

        $this->assertEquals($expected, $output);
    }

    public function test_method_tag_without_parameter(): void
    {
        $this->expectException(\Latte\CompileException::class);
        $this->expectExceptionMessage('Missing arguments in {method}');

        view('extension/method-tag-without-parameter')->render();
    }

    public function test_method_tag_unsupported_method(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Only PUT, PATCH and DELETE methods are possible via method spoofing');

        view('extension/method-tag', ['method' => 'FOO'])->render();
    }

    public function test_method_tag_get_method(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Do not use GET and POST method via method spoofing. Use the "method" form attribute instead.');

        view('extension/method-tag', ['method' => 'GET'])->render();
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

    public function test_dump_tag(): void
    {
        VarDumper::setHandler(function($var, $label){
            echo '<pre>' . var_export($var, true) . '</pre>';
        });

        $output = view('extension/dump-tag')->render();

        $this->assertEquals("<pre>'Dumped variable'</pre>", $output);
    }
}