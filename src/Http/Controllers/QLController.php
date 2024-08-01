<?php

namespace LaravelQL\LaravelQL\Http\Controllers;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class QLController extends Controller
{
    public function bind(Request $request){
        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'hello' => [
                    'type' => Type::string(),
                    'resolve' => function($rootVal , $args): string{
                        Log::info("HELLO IN RESOLVER");

                        return "HELLOS";
                    }
                ],
            ]
        ]);
        $schema = new Schema(
            (new SchemaConfig())->setQuery($queryType)
        );

        $query = $request->input('query');
        try {
            $result = GraphQL::executeQuery($schema , $query , [] , null , []);
            Log::info("",[$result]);
            $output = $result->toArray();
        }catch (\Exception $e){
            $output = [
              'errors' => [
                  'message' => $e->getMessage()
              ]
            ];
        }
        return response()->json($output);
    }
}
