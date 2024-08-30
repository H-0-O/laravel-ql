<?php

namespace LaravelQL\LaravelQL\Http\Controllers;

use App\Models\User;
use GraphQL\Error\DebugFlag;
use GraphQL\GraphQL;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Utils\SchemaPrinter;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use LaravelQL\LaravelQL\QLHandler;
use LaravelQL\LaravelQL\QLType;
use LaravelQL\LaravelQL\Registry\QLTypeLoader;
use stdClass;

class QLController extends Controller
{
    public function bind(Request $request)
    {

        // $user = new User;
        // dd($user->user());

        $qlHandler = QLHandler::getInstance();

        $userType = new ObjectType([
            'name' => 'userType',
            'fields' => [
                'fname' => [
                    'type' => Type::string(),
                    'resolve' => function ($rootVal, $args): string {
                        dd("IN USER TYPE ", $rootVal, $args);
                        return "Hossein";
                    },
                    'args' => [
                        'ee' => [
                            'type' => Type::string()
                        ]
                    ]
                ],
            ]
        ]);

        $config = [
            'name' => 'Query',
            'fields' => [
                // 'user' => [
                //     'type' => $userType,
                //     // 'args' => [
                //     //     'name' => [
                //     //         'type' => Type::string()
                //     //     ]
                //     // ],
                //     'resolve' => function ($r, $args) {
                //         $ne = new stdClass();
                //         $ne->name = "HOssein";
                //         // dd($r, $args);
                //         return $ne;
                //     }
                // ]
            ]
        ];

        foreach ($qlHandler->getTypesMap() as $type) {
            /** @var QLType $type */
            foreach ($type->getQueries() as $queryKey => $query) {
                $config['fields'][$queryKey] = $query;
            }
        }

        $queryType = new ObjectType(
            $config
        );

        $schema = new Schema(
            (new SchemaConfig())->setQuery($queryType)
        );

        $re = SchemaPrinter::doPrint($schema);

        // Log::info($re);
        // dd($config);
        $query = $request->input('query');
        try {
            $result = GraphQL::executeQuery($schema, $query, null, null, [])->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE);
            Log::info("", [$result]);
            $output = $result;
        } catch (\Exception $e) {
            $output = [
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
        return response()->json($output);
    }
}
