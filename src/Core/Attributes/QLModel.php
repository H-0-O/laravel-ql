<?php

namespace LaravelQL\LaravelQL\Core\Attributes;

use Attribute;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionUnionType;
use RuntimeException;

#[Attribute]
class QLModel
{
    /**
     * it uses as Type name in the schema 
     *
     * @var string
     */
    public string $typeName = "";

    public string $typeNameWithPath = "";

    public ReflectionClass $reflection;


    /**
     * This attribute tell Laravel-ql that , I want to use this model as Graphql 
     * @param string $DTO
     */
    public function __construct() {}


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
}
