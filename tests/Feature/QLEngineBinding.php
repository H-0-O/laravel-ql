<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Testbench\Attributes\WithMigration;
use function Orchestra\Testbench\workbench_path;


class QLEngineBinding extends TestCase
{
    use WithWorkbench;
    const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json'
    ];
    protected function getEnvironmentSetUp($app)
    {
        $app->setBasePath(dirname(__DIR__));
    }

    // protected function defineDatabaseMigrations()
    // {
    //     $this->loadMigrationsFrom(
    //         workbench_path('database/migrations')
    //     );
    // }

    public function testBind()
    {
        // Config::set([
        //     'DB_HOST' => 'db',
        //     'DB_CONNECTION' => 'mysql',
        //     'DB_DATABASE' => 'laravel-ql',
        //     'DB_USER' => 'root',
        //     'DB_PASS' => '123',
        //     'DB_PORT' => '3306'
        // ]);

        $re = $this->postJson('/graphql', [
            'query' => <<<'GQL'
                query{
                    user(id: 223){
                        fname

                    }
                }

             GQL

        ], self::HEADERS);

        var_dump($re->original);
        die();
    }
}
