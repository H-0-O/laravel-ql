<?php

namespace App\Models;


use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\Skip;

#[QLDTO]
class UserDTO
{

    public string $fname;
    public string $lname;
    // public User $user;
}
