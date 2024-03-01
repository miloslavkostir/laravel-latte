<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Foundation\Application;
use Latte\Engine as Latte;
use Latte\Runtime\Template;
use Livewire\LivewireManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/latte.php', 'latte'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/latte.php' => config_path('latte.php'),
        ]);

        // Latte
        $this->app->singleton(Latte::class, function ($app) {
            return $this->createLatte($app);
        });

        // LatteEngine
        $factory = $this->app['view'];
        $factory->addExtension('latte', 'latte', function () {
            return $this->createEngine();
        });
    }

    protected function createLatte(Application $app): Latte
    {
        $config = $this->app['config'];
        $latte = new Latte();

        $compiled = $config->get('latte.compiled') ?? $config->get('view.compiled');
        $latte->setTempDirectory($compiled ?: null);
        $latte->setAutoRefresh($config->get('latte.auto_refresh') ?? $config->get('app.debug', false));
        $latte->setStrictParsing($config->get('latte.strict_parsing'));
        $latte->setStrictTypes($config->get('latte.strict_types'));

        $finder = function (Template $template) use ($config) {
            if (!$template->getReferenceType() && $layout = $config->get('latte.layout')) {
                return $layout;
            }
        };
        $latte->addProvider('coreParentFinder', $finder);

        $latte->addExtension(new Extension());
        if ($app->has(LivewireManager::class)) {
            $latte->addExtension(new LivewireExtension());
        }

        return $latte;
    }

    protected function createEngine(): LatteEngine
    {
        return new LatteEngine($this->app[Latte::class]);
    }
}
