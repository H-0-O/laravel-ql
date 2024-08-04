<?php

namespace LaravelQL\LaravelQL\Core\Attributes;

use Attribute;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use ReflectionMethod;
use ReflectionUnionType;
use RuntimeException;

#[Attribute]
class QLModel
{
    public string $typeName = "";

    /** @var array<ReflectionMethod> */
    private array $queries = [];

    private array $mutations = [];

    public function initialQuery(){
        $this->queries = [
            'name' => $this->typeName,
            'description' => '',
            'fields' => []
        ];
    }

    public function initialMutation(){

    }


    /**
     * @throws InvalidReturnTypeException
     * @throws QueryMustHaveReturnTypeException
     */
    public function buildQuires(): array
    {

        $buildQueries = [];
        foreach ($this->queries as $queryMethod) {

            $returnType = $this->getReturnType($queryMethod);
            $fieldName = $queryMethod->getName();


            //TODO the return type aren't always scalar , and must search the QLContainer for the custom types
            $buildQueries['fields'][$fieldName] = [
                'resolve' => fn() => "Must write a dynamic resolver",
                'type'    => $returnType
            ];
        }

        return $buildQueries;
    }

    public function buildMutations()
    {

    }


    public function generateFields(ReflectionMethod $method)
    {
        $returnType = $method->getReturnType();
    }

    /**
     * @throws QueryMustHaveReturnTypeException
     * @throws InvalidReturnTypeException
     */
    private function getReturnType(ReflectionMethod $method): string
    {
        if (!$method->hasReturnType()) {
            throw new QueryMustHaveReturnTypeException("You must define a `return type` for $method->name in $method->class");
        }

        $returnType = $method->getReturnType();
        //TODO remove this condition later

        if ($returnType instanceof ReflectionUnionType) {
            throw new RuntimeException("Unions are not supported currently  ");
        }

        if ($returnType?->getName() === "mixed") {
            throw new InvalidReturnTypeException("The `mixed` return type not allowed in query $method->name in $method->class");
        }

        return $returnType->getName();
    }

    private function getQueryType(ReflectionMethod $method)
    {
        return $method->getReturnType();
    }

    public function addQueryMethod(ReflectionMethod $method): void
    {
        $this->queries[] = $method;
    }

    public function addMutationMethod(ReflectionMethod $method): void
    {
        $this->mutations[] = $method;
    }

}
