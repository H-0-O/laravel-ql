<?php

namespace LaravelQL\LaravelQL;

use Illuminate\Support\ServiceProvider;

class LaravelQLServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        if (env('LARAVEL_QL_DEBUG', false)) {
            config(['logging.channels.single.path' => dirname(__DIR__) . '/storage/logs/QL.log']);
        }
        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        $handler = QLHandler::getInstance();

        $handler->handle();
    }

    public function register()
    {
        parent::register();
    }
}
