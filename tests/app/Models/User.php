<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\Attributes\QLModel;

#[QLModel(UserDTO::class)]
class User extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    protected $guarded = [
        'password'
    ];
}
