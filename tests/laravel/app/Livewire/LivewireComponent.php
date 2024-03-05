<?php

namespace Miko\LaravelLatte\Tests\laravel\app\Livewire;

use Livewire\Component;

class LivewireComponent extends Component
{
    public $lorem = 'ipsum';

    public function __construct()
    {
    }

    public function render()
    {
        return view('livewire.livewire-component');
    }
}