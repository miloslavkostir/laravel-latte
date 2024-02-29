<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Foundation\Application;
use Latte\Engine as Latte;
use Livewire\LivewireManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
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

    private function createLatte(Application $app): Latte
    {
        $config = $this->app['config'];
        $latte = new Latte();

        $latte->setTempDirectory($config->get('view.compiled'));
        $latte->setAutoRefresh($config->get('latte.auto_refresh') ?? $config->get('app.debug', true));
        $latte->setStrictParsing($config->get('latte.strict_parsing', false));
        $latte->setStrictTypes($config->get('latte.strict_types', false));

        $finder = function (\Latte\Runtime\Template $template) use ($config) {
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

    private function createEngine(): LatteEngine
    {
        return new LatteEngine($this->app[Latte::class]);
    }
}
