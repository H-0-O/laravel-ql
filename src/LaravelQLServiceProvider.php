<?php

namespace LaravelQL\LaravelQL;

use Illuminate\Support\ServiceProvider;

class LaravelQLServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        dd("HELLO IN BOOT");
    }

    public function register()
    {
        parent::register(); // TODO: Change the autogenerated stub
    }

}
