<?php

declare(strict_types=1);

namespace Miko\LaravelLatte\Loaders;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewFinderInterface;
use Illuminate\View\ViewName;
use Latte\Loader;
use Latte\RuntimeException;
use Latte\TemplateNotFoundException;
use Throwable;
use function array_pop;
use function end;
use function explode;
use function implode;
use function preg_match;
use function str_starts_with;
use function strtr;
use const DIRECTORY_SEPARATOR;

class LaravelLoader implements Loader
{
    public function __construct(
        protected Factory $view
    ) {}

    public function finder(): ViewFinderInterface
    {
        return $this->view->getFinder();
    }

    public function filesystem(): Filesystem
    {
        $finder = $this->finder();
        if ($finder instanceof FileViewFinder) {
            return $finder->getFilesystem();
        }

        throw new RuntimeException('Latte requires a file-based view finder.');
    }

    public function getContent(string $name): string
    {
        $file = $this->resolve($name);

        if (! $this->filesystem()->isFile($file)) {
            throw new TemplateNotFoundException("Missing template file '$file'.");
        }

        try {
            return $this->filesystem()->get($file);
        } catch (Throwable $e) {
            throw new RuntimeException("Unable to read file '$file'.", previous: $e);
        }
    }

    public function isExpired(string $path, int $time): bool
    {
        try {
            return $this->filesystem()->lastModified($path) > $time;
        } catch (Throwable) {
            return true;
        }
    }

    public function getReferredName(string $name, string $referringFile): string
    {
        return $this->resolve($name, $referringFile);
    }

    public function getUniqueId(string $name): string
    {
        return strtr($this->resolve($name), '/', DIRECTORY_SEPARATOR);
    }

    protected function resolve(string $name, ?string $context = null): string
    {
        if ($this->looksLikePath($name)) {
            return $this->normalizePath($this->isRelativePath($name) && $context
                ? dirname($context) . '/' . $name
                : $name);
        }

        return $this->findViewPath($name);
    }

    protected function looksLikePath(string $name): bool
    {
        return str_starts_with($name, '/')
            || str_starts_with($name, './')
            || str_starts_with($name, '../')
            || preg_match('#^[a-z]:[\\/]#i', $name) === 1
            || str_starts_with($name, 'phar://');
    }

    protected function isRelativePath(string $name): bool
    {
        return str_starts_with($name, './') || str_starts_with($name, '../');
    }

    protected function findViewPath(string $name): string
    {
        $name = $this->normalizeViewName($name);

        try {
            return $this->finder()->find($name);
        } catch (Throwable $e) {
            throw new TemplateNotFoundException("Missing template view '$name'.", previous: $e);
        }
    }

    protected function normalizeViewName(string $name): string
    {
        return ViewName::normalize($name);
    }

    protected function normalizePath(string $path): string
    {
        preg_match('#^([a-z]:|phar://.+?/)?(.*)#i', $path, $m) ?: throw new \LogicException;

        $res = [];
        foreach (explode('/', strtr($m[2], '\\', '/')) as $part) {
            if ($part === '..' && $res && end($res) !== '..' && end($res) !== '') {
                array_pop($res);
            } elseif ($part !== '.') {
                $res[] = $part;
            }
        }

        return $m[1] . implode(DIRECTORY_SEPARATOR, $res);
    }
}
