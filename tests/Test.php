<?php

namespace Tests;


use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use Tests\Models\User;

class Test extends TestCase
{
    public function testHelloWorld(){
//        $user = new QLTypeGenerator(User::class);
        $re = $this->get("/");
        var_dump("THE RE :" , $re->baseResponse->status());
        die();
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
