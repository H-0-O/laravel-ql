<?php

namespace LaravelQL\LaravelQL;

use LaravelQL\LaravelQL\Core\RootQuery;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use stdClass;

class QLContainer
{
    private static RootQuery $rootQuery;
    private static array $models;

    public static array $types = [];

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
     * @throws ReflectionException
     */
    public static function generate(): void
    {

        //here we just create type to resolve when we are resolving Type that are dependent
        foreach (self::$models as $model) {
            $generator = new QLGenerator($model);
            if ($generator->createBaseConf()) {
                $re = new stdClass();
                $re->shortName = $generator->getqlmodelname();
                $re->ql = [];
                $re->generator = $generator;
                self::$types[$generator->getQLModelLongName()] = $re;
            }
        }

        foreach (self::$types as $type) {
            $type->generator->generate();
        }
    }


    private static function extractFromApp(string $str)
    {

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

    public static function &getCustomType(string $longName)
    {
        return self::$types[$longName]->ql;
    }
}
