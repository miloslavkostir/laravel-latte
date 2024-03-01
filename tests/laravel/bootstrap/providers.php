<?php

$envConfig = \Miko\LaravelLatte\Tests\TestEnvConfig::get();

$providers[] = \Miko\LaravelLatte\ServiceProvider::class;
if ($envConfig->livewire) {
    $providers[] = \Livewire\LivewireServiceProvider::class;
}

return $providers;
