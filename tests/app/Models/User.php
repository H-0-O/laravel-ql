<?php

namespace App\Models;

use App\Models\Game\Game;
use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\Attributes\QLModel;
use LaravelQL\LaravelQL\Core\Attributes\QLQuery;
use LaravelQL\LaravelQL\Core\Attributes\QLArray;

#[QLModel(UserDTO::class)]
class User extends Model
{


    protected $fillable = [
        'name',
    ];

    protected $guarded = [
        'password'
    ];


    // #[QLQuery]
    // #[QLArray('string')]
    // public function games(): ?array
    // {
    //     return [
    //         'hossein',
    //     ];
    // }

    //these must add to RootQuery
    #[QLQuery]
    public function user(int $id, string $name = "Hello"): string
    {
        return $name ?? "empty";
    }

    #[QLQuery]
    public function users(): ?Game
    {
        return new Game();
    }


    #[QLMutation]
    public function createUser() {}

    #[QLMutation]
    public function mutUpdateUser() {}

    #[QLMutation]
    public function mutDeleteUser() {}
}
