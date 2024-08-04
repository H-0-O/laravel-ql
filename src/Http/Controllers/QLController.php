<?php

namespace LaravelQL\LaravelQL\Http\Controllers;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class QLController extends Controller
{
    public function bind(Request $request){
        $userType = new ObjectType([
            'name' => 'userType',
            'fields' => [
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function($rootVal , $args): string{
                        return "Hossein";
                    }
                ],
            ]
        ]);

        $queryType = new ObjectType([
            'name' => 'Query',
            'fields' => [
                'user' => [
                    'type' => $userType,
                    'resolve' => fn() => ""
                ],
            ]
        ]);

        $schema = new Schema(
            (new SchemaConfig())->setQuery($queryType)
        );
        $re = SchemaPrinter::doPrint($schema);
//        Log::info($re);
//        dd();
        $query = $request->input('query');
        try {
            $result = GraphQL::executeQuery($schema , $query , $userType , null , []);
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
