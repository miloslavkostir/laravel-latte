<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Latte\Bridges\Tracy\TracyExtension;
use Latte\Engine as Latte;
use Latte\Feature;
use Latte\Runtime\Template;
use Livewire\LivewireManager;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected ConfigRepository $config;

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/latte.php', 'latte'
        );
    }

    public function boot(): void
    {
        $this->config = $this->app->get('config');

        $this->publishes([
            __DIR__ . '/../config/latte.php' => config_path('latte.php'),
        ]);

        // Latte
        $this->app->singleton(Latte::class, function (Application $app) {
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
        $config = $this->config;
        $compiled = $config->get('latte.compiled') ?? $config->get('view.compiled');

        $latte->setCacheDirectory($compiled ?: null);
        $latte->setAutoRefresh($this->decideAutoRefresh());
        $latte->setFeature(Feature::StrictParsing, $config->get('latte.strict_parsing'));
        $latte->setFeature(Feature::StrictTypes, $config->get('latte.strict_types'));
        $latte->setFeature(Feature::ScopedLoopVariables, $config->get('latte.scoped_loop_variables'));
        $latte->setFeature(Feature::Dedent, $config->get('latte.dedent'));
        $latte->setFeature(Feature::MigrationWarnings, $this->decideMigrationWarnings());

        $latte->addProvider('coreParentFinder', function (Template $template) use ($config) {
            if (!$template->getReferenceType() && $layout = $config->get('latte.layout')) {
                return $layout;
            }
        });
    }

    protected function decideAutoRefresh(): bool
    {
        return $this->config->get('latte.auto_refresh') ?? $this->config->get('app.debug', false);
    }

    protected function decideMigrationWarnings(): bool
    {
        return $this->config->get('latte.migration_warnings') ?? $this->config->get('app.debug', false);
    }

    protected function extensions(Latte $latte): void
    {
        // Regular extension for Laravel
        $latte->addExtension(new Extension($this->config->get('latte')));

        // Translation
        $latte->addExtension(new TranslationExtension($this->decideAutoRefresh()));

        // Livewire support
        if ($this->app->has(LivewireManager::class)) {
            $latte->addExtension(new LivewireExtension());
        }

        // Tracy debugger support
        if (class_exists('Tracy\Debugger') && \Tracy\Debugger::isEnabled()) {
            $latte->addExtension(new TracyExtension());
        }
    }
}
