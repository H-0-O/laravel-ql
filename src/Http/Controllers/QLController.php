<?php

namespace LaravelQL\LaravelQL\Http\Controllers;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class QLController extends Controller
{
    public function bind(Request $request){
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'hello' => Type::string(),
                'resolve' => fn($rootValue, array $args): string => 'Hello World'
            ]
        ]);
        $schema = new Schema([
            'query' => $queryType
        ]);

        $query = $request->input('query');
        return response()->json([
            'message' => config('logging.channels.single.path'),
            'path' => dirname(__DIR__)
        ]);
    }
}
