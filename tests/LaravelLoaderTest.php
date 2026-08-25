<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Tests;

use Latte\Engine as Latte;
use Latte\Loaders\FileLoader;
use Latte\TemplateNotFoundException;
use Miko\LaravelLatte\Loaders\LaravelLoader;
use Miko\LaravelLatte\Tests\Fixtures\CustomLoader;

class LaravelLoaderTest extends TestCase
{
    public function test_not_configured_loader_uses_nette_loader(): void
    {
        $loader = $this->app->get(Latte::class)->getLoader();

        $this->assertInstanceOf(FileLoader::class, $loader);
    }

    public function test_configured_laravel_loader(): void
    {
        $this->app['config']->set('latte.loader', 'laravel');

        $loader = $this->app->get(Latte::class)->getLoader();

        $this->assertInstanceOf(LaravelLoader::class, $loader);
    }

    public function test_configured_custom_loader(): void
    {
        $this->app['config']->set('latte.loader', CustomLoader::class);

        $loader = $this->app->get(Latte::class)->getLoader();

        $this->assertInstanceOf(CustomLoader::class, $loader);
    }

    public function test_default_nette_loader_does_not_resolve_laravel_dotted_includes(): void
    {
        $this->expectException(TemplateNotFoundException::class);

        view('laravel-loader/default-nette')->render();
    }

    public function test_laravel_file_loader_resolves_referred_names(): void
    {
        $loader = $this->loader();
        $referringFile = resource_path('views/laravel-loader/index.latte');

        $this->app['view']->addNamespace('laravel-loader', resource_path('views/vendor/laravel-loader'));
        $this->app['view']->addLocation(base_path('alternate-views'));

        $this->assertSame(
            resource_path('views/foo.latte'),
            $loader->getReferredName('foo', $referringFile)
        );
        $this->assertSame(
            resource_path('views/laravel-loader/partials/card.latte'),
            $loader->getReferredName('laravel-loader.partials.card', $referringFile)
        );
        $this->assertSame(
            resource_path('views/vendor/laravel-loader/namespaced.latte'),
            $loader->getReferredName('laravel-loader::namespaced', $referringFile)
        );
        $this->assertSame(
            base_path('alternate-views/alternate/location.latte'),
            $loader->getReferredName('alternate.location', $referringFile)
        );
        $this->assertSame(
            resource_path('views/laravel-loader/relative-fragment.latte'),
            $loader->getReferredName('./relative-fragment.latte', $referringFile)
        );
        $this->assertSame(
            resource_path('views/laravel-loader/foo.bar'),
            $loader->getReferredName('./foo.bar', $referringFile)
        );
        $this->assertSame(
            resource_path('views/laravel-loader/absolute-fragment.latte'),
            $loader->getReferredName(resource_path('views/laravel-loader/absolute-fragment.latte'), $referringFile)
        );
    }

    public function test_laravel_file_loader_reads_content(): void
    {
        $loader = $this->loader();

        $this->assertSame("CARD {\$title}\n", $loader->getContent('laravel-loader.partials.card'));
        $this->assertSame("ABSOLUTE\n", $loader->getContent(resource_path('views/laravel-loader/absolute-fragment.latte')));
        $this->assertSame(
            resource_path('views/laravel-loader/partials/card.latte'),
            $loader->getUniqueId('laravel-loader.partials.card')
        );
    }

    public function test_laravel_loader_renders_templates_with_laravel_view_resolution(): void
    {
        $this->app['config']->set('latte.loader', 'laravel');
        $this->app['view']->addNamespace('laravel-loader', resource_path('views/vendor/laravel-loader'));
        $this->app['view']->addLocation(base_path('alternate-views'));

        $output = view('laravel-loader.index', [
            'title' => 'Blade Style',
            'absoluteView' => resource_path('views/laravel-loader/absolute-fragment.latte'),
        ])->render();

        $expected = <<<HTML
        A:BLOCK Blade Style
        B:CARD Blade Style

        C:RELATIVE

        D:ABSOLUTE

        E:NAMESPACE Blade Style

        F:LOCATION Blade Style

        G:ROOT Blade Style
        HTML;

        $this->assertSame($expected, trim($output));
    }

    private function loader(): LaravelLoader
    {
        return new LaravelLoader($this->app->get('view'));
    }
}
