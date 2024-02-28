<?php

namespace Miko\LaravelLatte\Tests\laravel\app;

class TestController
{
    public function link($id)
    {
        return view('link-tag');
    }

    public function nHref($id)
    {
        return view('n-href-tag');
    }

    public function index()
    {
        return response('INDEX');
    }

    public function detail($id)
    {
        return response('DETAIL');
    }
}