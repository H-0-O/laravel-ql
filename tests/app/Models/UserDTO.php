<?php

namespace App\Models;


use LaravelQL\LaravelQL\Core\Attributes\DTO;
use LaravelQL\LaravelQL\Core\Attributes\Skip;

#[DTO]
class UserDTO
{
    public string|int|User $name;

    #[Skip]
    public string $rel_id;
}
