<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\QLModel;

#[QLModel]
class User extends Model
{
    const QL_Name = "User";


    protected $fillable = [
      'name',
      ''
    ];

    protected $guarded = [
      'password'
    ];


    public function queryUser(): string{
        return  "H";
    }

    public function queryUsers(): array{

        return $this;
    }

    public function mutCreateUser(){

    }

    public function mutUpdateUser(){

    }

    public function mutDeleteUser(){

    }


}
