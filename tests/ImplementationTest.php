<?php

namespace Miko\LaravelLatte\Tests;

class ImplementationTest extends TestCase
{
    public function test_latte_implemented(): void
    {
        $this->app['config']->set('latte.migration_warnings', false);

        $output = view('implementation/implemented', ['foo' => 'Bar', 'title' => null])->render();

        $latte_3_1 = \Latte\Engine::VersionId >= 30100;
        $expected = $this->getExpected($latte_3_1 ? 'implemented_3.1' : 'implemented_3.0');

        $this->assertEquals($expected, $output);
    }

    public function test_latte_implemented_with_warning(): void
    {
        $latte_3_1 = \Latte\Engine::VersionId >= 30100;
        if ($latte_3_1) {
            $this->expectException(\ErrorException::class);
            $this->expectExceptionMessage('Behavior change for attribute \'title\' with value null: previously it rendered as title="", now the attribute is omitted');
        }

        $this->app['config']->set('latte.migration_warnings', true);
        $output = view('implementation/implemented', ['foo' => 'Bar', 'title' => null])->render();

        $expected = $this->getExpected($latte_3_1 ? 'implemented_3.1' : 'implemented_3.0');

        $this->assertEquals($expected, $output);
    }

    public function test_blade(): void
    {
        $output = view('implementation/homepage', ['foo' => 'Bar'])->render();

        $this->assertEquals('Hello Bar', $output);
    }
}