<?php

namespace Miko\LaravelLatte\Tests\laravel\app\Http\Controllers\Admin;

class TestController
{
    public function detail($id)
    {
        return response('DETAIL');
    }
}