<?php

namespace LaravelQL\LaravelQL;

use App\Models\User;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class QLContainer
{
    private static array $models;


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
     * @throws \ReflectionException
     */
    public static function generate(){
        $final = [];
        foreach (self::$models as $model) {
            new QLGenerator($model);
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
