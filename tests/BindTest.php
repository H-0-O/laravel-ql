<?php

namespace Tests;

//use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\TestCase;
class BindTest extends TestCase
{
    function testQLServer(){
        $res = $this->get("/");

        $res->assertStatus(200);
    }
}
