<?php

namespace Miko\LaravelLatte\Tests;

class ImplementationTest extends TestCase
{
    public function test_latte_implemented(): void
    {
        $output = view('implementation/implemented', ['foo' => 'Bar'])->render();

        $expected = $this->getExpected('implemented');

        $this->assertEquals($expected, $output);
    }

    public function test_blade(): void
    {
        $output = view('implementation/homepage', ['foo' => 'Bar'])->render();

        $this->assertEquals('Hello Bar', $output);
    }
}