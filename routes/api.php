<?php


use Illuminate\Support\Facades\Route;
use LaravelQL\LaravelQL\Http\Controllers\QLController;

Route::post("/" , function(){
    return "Hooray  after few hours write a simple test";
});


Route::post("/bind" , [QLController::class , 'bind']);
