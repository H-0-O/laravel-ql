<?php

namespace App\Models;

use App\Models\Game\Game;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\QLArray;

#[QLDTO]
class UserDTO
{
    #[QLArray('string')]
    public ?array $games;

    public ?string $fname;

    public string $lname;

    public Game $game;
}
