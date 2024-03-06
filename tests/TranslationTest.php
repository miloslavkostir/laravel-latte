<?php

namespace Miko\LaravelLatte\Tests;

use Illuminate\Support\Facades\App;
use Miko\LaravelLatte\Runtime\Translator;

class TranslationTest extends TestCase
{
    public function tests_translator_without_parameters(): void
    {
        $this->expectException(\ArgumentCountError::class);
        $this->expectExceptionMessage('Too few arguments to function');

        $this->assertEquals('', Translator::translate());
    }

    public function tests_translator_as_laravel_trans(): void
    {
        App::setLocale('en');

        $this->assertEquals('Welcome to our application!', Translator::translate('messages.welcome'));
        $this->assertEquals('Welcome to our application!', Translator::translate('messages.welcome', []));
        $this->assertEquals('Hello JOHN', Translator::translate('messages.greeting', ['name' => 'John']));
        $this->assertEquals('Ahoj JOHN', Translator::translate('messages.greeting', ['name' => 'John'], 'cs'));
        $this->assertEquals('Vítejte v naší aplikaci!', Translator::translate('messages.welcome', locale: 'cs'));
    }

    public function tests_translator_as_laravel_trans_choice(): void
    {
        App::setLocale('en');

        $this->assertEquals('There is one apple', Translator::translate('messages.apples', 1));
        $this->assertEquals('There is one apple', Translator::translate('messages.apples', 1, []));
        $this->assertEquals('5 minutes ago', Translator::translate('messages.minutes_ago', 5, ['value' => 5]));
        $this->assertEquals('před 5 minutami', Translator::translate('messages.minutes_ago', 5, ['value' => 5], 'cs'));
        $this->assertEquals('There are 7', Translator::translate('messages.count', 7));
        $this->assertEquals('Je tam jedno jablko', Translator::translate('messages.apples', 1, locale: 'cs'));
    }

    public function tests_translation_tag(): void
    {
        $this->app['config']->set('app.locale', 'cs');

        $output = view('translation/translation-tag')->render();

        $expected = $this->getExpected('translation-tag-cs');

        $this->assertEquals($expected, $output);
    }

    public function tests_translation_tag_with_set_locale(): void
    {
        $this->app['config']->set('app.locale', 'cs');

        // The locale has been changed at runtime
        App::setLocale('en');

        $output = view('translation/translation-tag')->render();

        $expected = $this->getExpected('translation-tag-en');

        $this->assertEquals($expected, $output);
    }
}