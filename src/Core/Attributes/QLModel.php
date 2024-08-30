<?php

namespace LaravelQL\LaravelQL\Core\Attributes;

use Attribute;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use LaravelQL\LaravelQL\Util;
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
     * here we hold function that has #[QLQuery] Attribute
     * @var array
     */
    private array $queries = [];

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


    public function generateQuires(): void
    {
        $methods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, function (ReflectionMethod $method) {
            $attributes = $method->getAttributes(QLQuery::class);
            return count($attributes) > 0;
        });

        $class = $this->reflection->getName();
        foreach ($methods as $method) {
            if (!$method->hasReturnType()) {
                throw new QueryMustHaveReturnTypeException("You must define a `return type` for $method->name in $class");
            }

            $type = Util::resolveType($method->getReturnType(), $method->getAttributes(), $method->getName(), $class);
            $methodName = $method->getName();
            $modelClassName = $this->reflection->getName();
            $args = Util::getQueryArgs($method);

            //TODO must write dynamic resolver
            $this->queries[$methodName] = [
                'type' => $type,
                'resolve' => static function ($rootVal, $args) use ($modelClassName, $methodName) {
                    //TODO we must create a debug situation to log resolvers
                    $object = resolve($modelClassName);
                    return call_user_func_array([$object, $methodName], $args);
                },
                'args' => $args
            ];
        }
    }


    public function getQueries(): array
    {
        return $this->queries;
    }
}
