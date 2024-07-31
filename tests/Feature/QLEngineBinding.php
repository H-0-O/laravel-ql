<?php

namespace Tests\Feature;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

class QLEngineBinding extends TestCase
{
    use WithWorkbench;
    public function testBind(){

        $re = $this->post('/bind' , [] , [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]);

        $re->assertJson([
           'message' => "Hello in bind"
        ]);
    }
}
