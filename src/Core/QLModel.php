<?php

namespace LaravelQL\LaravelQL\Core;

use Attribute;
use Exception;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use ReflectionMethod;
use ReflectionUnionType;
use RuntimeException;

#[Attribute]
class QLModel
{
    const QUERY_TYPE_NAME = "name";

    /** @var array<ReflectionMethod> */
    public array $queries = [];

    public array $mutations = [];

    /**
     * @throws InvalidReturnTypeException
     * @throws QueryMustHaveReturnTypeException
     */
    public function buildQuires(): array
    {
        $buildQueries = [];
        foreach ($this->queries as $query) {
            $this->queryCheck($query);


            $typeName = $query->getName();
            //TODO must move to model self , it's not related to select in query
//            $buildQueries[]['name'] = $typeName;

            $buildQueries[]['fields'][$typeName] = [
                'resolve' => fn() => "Must write a dynamic resolver",
                'type' => ''
            ];
        }

        return $buildQueries;
    }

    public function buildMutations()
    {

    }


    public function generateFields(ReflectionMethod $method){
        $returnType = $method->getReturnType();
    }

    /**
     * @throws QueryMustHaveReturnTypeException
     * @throws InvalidReturnTypeException
     */
    private function queryCheck(ReflectionMethod $method): void
    {
        if (!$method->hasReturnType()) {
            throw new QueryMustHaveReturnTypeException("You must define a `return type` for $method->name in $method->class");
        }
        if($method->getReturnType() instanceof  ReflectionUnionType){
            throw new RuntimeException("Unions are not supported currently  ");
        }

        if ($method->getReturnType()?->getName() === "mixed") {
            throw new InvalidReturnTypeException("The `mixed` return type not allowed in query $method->name in $method->class");
        }
    }

    private function getQueryType(ReflectionMethod $method){
        return $method->getReturnType();
    }
}
