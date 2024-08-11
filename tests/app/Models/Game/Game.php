<?php

namespace App\Models\Game;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\Attributes\QLModel;

#[QLModel(GameDTO::class)]
class Game extends Model {}
