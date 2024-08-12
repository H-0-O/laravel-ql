<?php

namespace LaravelQL\LaravelQL\Core\Attributes;

/**
 * When you wan't to tell the QL that my field is array you must specify type of array something like this `array<T>` 
 * and you must pass the T in QLArray ex : `#[QLArray('string')]` or `#[QLArray(User::class)]` 
 * and when you want to tell the ql my T sometimes are `null` 
 * you must pass your type with an bool that tells it's `nullable` or not
 * ex: `#[QLArray('string' , true)]` or `#[QLArray(User::class , true)]`,
 * 
 *  **Note**: the second parameter is `nullable` and it's `false` by default 
 * 
 */
class QLArray
{
    public function __construct(
        public string $type,
        public bool $nullable = false
    ) {}
}
