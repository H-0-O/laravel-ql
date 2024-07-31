<?php

namespace LaravelQL\LaravelQL\Core;

use Attribute;

#[Attribute]
class QLModel
{
    public function __construct()
    {
        echo "HELLO IN CON";
    }
}
