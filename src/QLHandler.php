<?php

namespace LaravelQL\LaravelQL;

use GraphQL\Type\Definition\ObjectType;
use Illuminate\Foundation\Mix;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionException;
use stdClass;

class QLHandler
{
    private static self|null $instance = null;

    /**
     * here we hold the types(none root type) , we keep dto class name as a key (name of type) , and the value of it is the instance of QLType
     *
     * @var array
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
    }

    public function grepModels(): array
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
        // dd(array_keys($this->typesMap));
        foreach ($this->typesMap as $type) {

            /** @var QLType $type */
            $type->initObjectType();
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
}
