<?php

namespace App\Models;

use App\Models\Game\Game;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\QLUnion;
use LaravelQL\LaravelQL\Core\Attributes\Skip;

#[QLDTO]
class UserDTO
{

    public ?string $fname;

    public string $lname;

    public Game $game;
}
