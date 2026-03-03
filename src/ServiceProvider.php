<?php

declare(strict_types=1);

namespace Miko\LaravelLatte;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Foundation\Application;
use Latte\Bridges\Tracy\TracyExtension;
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

        // Latte Engine Singleton
        $this->app->singleton(Latte::class, function (Application $app) {
            return $this->createLatte($app);
        });

        // Register Latte Extension in View Factory
        $factory = $this->app->get('view');
        $factory->addExtension('latte', 'latte', function () {
            return new LatteEngine($this->app->get(Latte::class));
        });
    }

    protected function createLatte(Application $app): Latte
    {
        $latte = new Latte();
        $config = $app->get('config');

        $this->configure($latte, $config);
        $this->extensions($latte, $app, $config);

        return $latte;
    }

    protected function configure(Latte $latte, ConfigRepository $config): void
    {
        $compiled = $config->get('latte.compiled') ?? $config->get('view.compiled');

        // Cache directory (Latte 3.1+)
        if (method_exists($latte, 'setCacheDirectory')) {
            $latte->setCacheDirectory($compiled ?: null);
        } else {
            $latte->setTempDirectory($compiled ?: null);
        }

        $latte->setAutoRefresh($this->decideAutoRefresh($config));
        $latte->setStrictParsing((bool) $config->get('latte.strict_parsing', false));

        $this->setupFeatures($latte, $config);

        $latte->addProvider('coreParentFinder', function (Template $template) use ($config) {
            if (!$template->getReferenceType() && $layout = $config->get('latte.layout')) {
                return $layout;
            }
        });
    }

    protected function setupFeatures(Latte $latte, ConfigRepository $config): void
    {
        $strictTypes = (bool) $config->get('latte.strict_types', true);

        if (!class_exists('Latte\Feature')) {
            $latte->setStrictTypes($strictTypes);
            return;
        }

        $latte->setFeature(\Latte\Feature::StrictTypes, $strictTypes);
        $latte->setFeature(\Latte\Feature::MigrationWarnings, (bool) $config->get('latte.migration_warnings', false));
    }

    protected function decideAutoRefresh(ConfigRepository $config): bool
    {
        return (bool) ($config->get('latte.auto_refresh') ?? $config->get('app.debug', false));
    }

    protected function extensions(Latte $latte, Application $app, ConfigRepository $config): void
    {
        // Regular extension for Laravel
        $latte->addExtension(new Extension((array) $config->get('latte', [])));

        // Translation
        $latte->addExtension(new TranslationExtension($this->decideAutoRefresh($config)));

        // Livewire support
        if ($app->has(LivewireManager::class)) {
            $latte->addExtension(new LivewireExtension());
        }

        // Tracy debugger support
        if (class_exists('Tracy\Debugger') && \Tracy\Debugger::isEnabled()) {
            $latte->addExtension(new TracyExtension());
        }
    }
}
