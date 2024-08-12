<?php

namespace LaravelQL\LaravelQL;

use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;

class QLHandler
{
    private static self|null $instance = null;

    /**
     * here we hold the types(none root type) , we keep dto class name as a key (name of type) , and the value of it is the instance of QLType
     *
     * @var QLType[]
     */
    private array $typesMap = [];



    public static function getInstance()
    {
        $instance = self::$instance;
        if ($instance) {
            return $instance;
        }
        self::$instance = new self();
        return self::$instance;
    }


    public function handle()
    {
        $modelsPath = $this->grepModels();
        $this->initialTypes($modelsPath);
        $this->generateNoneRootTypes();
        $this->generateRootTypes();
    }

    private function grepModels(): array
    {
        if (env('APP_ENV') == 'local') {
            $path = base_path() . '/app/Models/';
            $files = [];
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $this->convertPathToClass($this->extractFromApp($file->getPathname()));
                }
            }
            return $files;
        } else {
            return [];
        }
    }

    /**
     * this method just create
     * @throws ReflectionException
     */
    private function initialTypes($modelsPath): void
    {

        //here we just create type to resolve when we are resolving Type that are dependent
        foreach ($modelsPath as $modelPath) {
            $qlType = new QLType($modelPath);
            if ($qlType->initQLModel()) {
                $qlType->initQLDTO();
                $this->typesMap[$qlType->getTypeNameWithPath()] = $qlType;
            }
        }
    }


    private function generateNoneRootTypes(): void
    {
        foreach ($this->typesMap as $type) {
            $type->initObjectType();
        }
    }

    private function generateRootTypes()
    {
        foreach ($this->typesMap as $type) {
            $type->initQueries();
        }
    }

    private function extractFromApp(string $str): string
    {

        // Find the position of the word 'app'
        $start = strpos($str, 'app');

        // Extract the substring starting from 'app'
        $result = substr($str, $start + strlen('app/'));

        // Prepend 'App/' to the result
        return 'App/' . $result;
    }

    private function convertPathToClass($path): string
    {
        return str_replace(array("/", ".php"), array("\\", ""), $path);
    }


    public function getTypesMap(): array
    {
        return $this->typesMap;
    }

    public function __call($name, $arguments): ObjectType|NonNull
    {
        $allowNull = $arguments[0];
        $type = $this->typesMap[$name]->getObjectType();
        return $allowNull ?  $type : Type::nonNull($type);
    }
}
