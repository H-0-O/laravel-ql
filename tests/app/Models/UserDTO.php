<?php

namespace App\Models;

use App\Models\Game\Game;
use Illuminate\Support\Facades\Config;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\QLArray;
use LaravelQL\LaravelQL\Core\Attributes\QLQuery;

#[QLDTO]
class UserDTO
{
    public function __construct(private User $user) {}

    #[QLArray('string')]
    public ?array $games;

    public ?string $fname;

    public string $lname;

    public Game $game;

    #[QLQuery]
    public function user(int $id): ?User
    {
        return $this->user::find($id);
    }

    #[QLMutation]
    public function createUser() {}
}
