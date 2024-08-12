<?php

namespace App\Models;

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


    #[QLQuery]
    #[QLArray('int')]
    public function games(): array
    {
        return [];
    }

    //these must add to RootQuery
    #[QLQuery]
    #[QLArray]
    public function user(): string
    {
        return  "H";
    }

    #[QLQuery]
    public function users(): self
    {
        return $this;
    }


    #[QLMutation]
    public function createUser() {}

    #[QLMutation]
    public function mutUpdateUser() {}

    #[QLMutation]
    public function mutDeleteUser() {}
}
