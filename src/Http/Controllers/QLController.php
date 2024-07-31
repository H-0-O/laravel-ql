<?php

namespace LaravelQL\LaravelQL\Http\Controllers;
use Illuminate\Routing\Controller;
class QLController extends Controller
{
    public function bind(){
        return response()->json([
            'message' => 'Hello in bind'
        ]);
    }
}
