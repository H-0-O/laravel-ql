<?php

namespace App\Models\Game;

use App\Models\User;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;

#[QLDTO]
class GameDTO
{
    public string $name;
    public string $publishDate;
    public float $price;
    public User $user;
}
