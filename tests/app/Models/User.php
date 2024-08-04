<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\QLModel;
use LaravelQL\LaravelQL\Core\Types;

#[QLModel]
class User extends Model
{
    const QL_Name = "User";

    #[QLFields]
    protected $fillable = [
      'name',
    ];

    protected $guarded = [
      'password'
    ];


//    const fields = [
//        'name' => [Types::String , Types::Int],
//        'age' => Types::Int,
//        'friends' => Types::Array
//    ];



    public function queryUser(): string{
        return  "H";
    }

    public function queryUsers(): self{

        return $this;
    }

    public function mutCreateUser(){

    }

    public function mutUpdateUser(){

    }

    public function mutDeleteUser(){

    }


}
