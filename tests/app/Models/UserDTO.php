<?php

namespace App\Models;

use App\Models\Game\Game;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\QLArray;
use LaravelQL\LaravelQL\Core\Attributes\QLQuery;
use LaravelQL\LaravelQL\Core\Attributes\QLMutation;

#[QLDTO]
class UserDTO
{
    public function __construct(private User $user) {}

    public string $name;

    // #[QLArray('string')]
    // public ?array $games;

    // public ?string $fname;

    // public string $lname;

    // public Game $game;

    #[QLQuery]
    public function user(int $id): ?User
    {
        return $this->user::find($id);
    }

    #[QLMutation]
    public function createUser(): User
    {
        $user = User::create([
            'name' => 'Hossein',
            'email' => 'test@gmail.com',
        ]);
        return $user;
    }
}
