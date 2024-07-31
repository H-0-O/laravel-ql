<?php

namespace Tests;


use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\QLTypeGenerator;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use tests\Models\User;

class Test extends TestCase
{
    public function testHelloWorld(){
        $user = new QLTypeGenerator(User::class);
    }

    //FIXME : it's just for now , after a while it must move to execute in a one route
    private function handleQlModel(Model $model){
        $reflection = new ReflectionClass($model);
        $constants = $reflection->getConstants();

        var_dump($constants);
        die();
//        $type = new QLType();
    }

}
