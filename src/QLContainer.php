<?php

namespace LaravelQL\LaravelQL;

use App\Models\User;
use LaravelQL\LaravelQL\Core\RootQuery;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;

class QLContainer
{
    private static RootQuery $rootQuery;
    private static array $models;

    private static array $types = [];

    public static function setModels(array $models)
    {
        self::$models = $models;
    }
    public static function grepModels()
    {
        if (env('APP_ENV') == 'local') {
            $path = base_path().'/app/Models/';
            $files = [];
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = self::convertPathToClass(self::extractFromApp($file->getPathname()));
                }
            }
            self::$models = $files;
        } else {
            self::$models = [];
        }
    }

    /**
     * this method just create
     * @throws InvalidReturnTypeException
     * @throws QueryMustHaveReturnTypeException
     * @throws ReflectionException
     */
    public static function generate(){

        //here we just create type to resolve when we are resolving Type that are dependent
        foreach (self::$models as $model) {
           $generator =  new QLGenerator($model);
           if($generator->createBaseConf()){
                 self::$types[$generator->getQLModelName()] = $generator;
           }
        }

        foreach (self::$types as $type) {
            $type->generate();
        }
    }


    private static function extractFromApp(string $str){

        // Find the position of the word 'app'
        $start = strpos($str, 'app');

        // Extract the substring starting from 'app'
        $result = substr($str, $start + strlen('app/'));

        // Prepend 'App/' to the result
        return 'App/'.$result;
    }

    private static function convertPathToClass($path): string
    {
        return str_replace(array("/", ".php"), array("\\", ""), $path);
    }
}
