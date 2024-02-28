<?php

namespace Miko\LaravelLatte\Tests\laravel\app;

use Livewire\Component;

class MyComponent extends Component
{
    public $lorem = 'ipsum';

    public function render()
    {
        return view('livewire.my-component');
    }
}