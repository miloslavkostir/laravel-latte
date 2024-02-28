<?php

namespace Miko\LaravelLatte\Tests\laravel\app\Admin;

class TestController
{
    public function detail($id)
    {
        return response('DETAIL');
    }
}