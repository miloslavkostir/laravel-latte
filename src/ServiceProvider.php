<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Foundation\Application;
use Latte\Bridges\Tracy\TracyExtension;
use Latte\Engine as Latte;
use Latte\Essential\TranslatorExtension;
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
        $factory = $this->app->get('view');
        $factory->addExtension('latte', 'latte', function () {
            return $this->createEngine();
        });
    }

    protected function createLatte(Application $app): Latte
    {
        $latte = new Latte();

        $this->configure($latte);
        $this->extensions($latte);

        return $latte;
    }

    protected function createEngine(): LatteEngine
    {
        return new LatteEngine($this->app->get(Latte::class));
    }

    protected function configure(Latte $latte): void
    {
        $config = $this->app->get('config');
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
    }

    protected function extensions(Latte $latte): void
    {
        // Regular extension for Laravel
        $latte->addExtension(new Extension());

        // Translation
        $latte->addExtension(new TranslatorExtension([Translator::class, 'translate']));

        // Livewire
        if ($this->app->has(LivewireManager::class)) {
            $latte->addExtension(new LivewireExtension());
        }

        // Tracy debugger
        if (class_exists('Tracy\Debugger') && \Tracy\Debugger::isEnabled()) {
            $latte->addExtension(new TracyExtension());
        }
    }
}
