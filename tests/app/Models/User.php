<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\Attributes\QLModel;
use LaravelQL\LaravelQL\Core\Types;

#[QLModel(UserDTO::class)]
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

    #[QLFields]
    private $fields = [
        'name' => Types::String,
        'user' => [User::class  , Types::String , null],
    ];


    //these must add to RootQuery
    #[QLQuery]
    public function user(): string{
        return  "H";
    }

    #[QLQuery]
    public function users(): self{
        return $this;
    }

    #[QLMutation]
    public function createUser(){

    }

    #[QLMutation]
    public function mutUpdateUser(){

    }

    #[QLMutation]
    public function mutDeleteUser(){

    }


}
