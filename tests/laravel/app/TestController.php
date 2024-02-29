<?php

namespace Miko\LaravelLatte\Tests\laravel\app;

class TestController
{
    public function link($id)
    {
        return view('extension.link-tag');
    }

    public function nHref($id)
    {
        return view('extension.n-href-tag');
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