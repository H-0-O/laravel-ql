<?php

namespace tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\QLModel;

#[QLModel]
class User extends Model
{
    const QL_Name = "User";
}
