<?php

namespace App\Models;


use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\Skip;

#[QLDTO]
class UserDTO
{
    public User $LName;
    public string|int|User $name;

    #[Skip]
    public string $rel_id;
}
