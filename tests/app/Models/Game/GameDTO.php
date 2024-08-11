<?php

namespace App\Models\Game;

use LaravelQL\LaravelQL\Core\Attributes\QLDTO;

#[QLDTO]
class GameDTO
{
    public string $name;
    public string $publishDate;
    public float $price;
}
