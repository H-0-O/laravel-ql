<?php

namespace Tests\Feature;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class QLEngineBinding extends TestCase
{
    use WithWorkbench;
    const HEADERS = [
        'Content-Type' => 'application/json',
        'Accept'       => 'application/json'
    ];
    public function testBind(){
        $re = $this->postJson('/graphql' , [
            'query' => <<<GQL
                query {
                    hello
                }

             GQL

        ] , self::HEADERS);

        var_dump($re->original);
        die();
    }
}
