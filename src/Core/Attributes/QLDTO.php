<?php

namespace LaravelQL\LaravelQL\Core\Attributes;

use ArgumentCountError;
use Attribute;
use GraphQL\Error\FormattedError;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use LaravelQL\LaravelQL\Util;
use ReflectionClass;
use ReflectionProperty;
use ReflectionMethod;
use RuntimeException;

#[Attribute]
class QLDTO
{
    public ReflectionClass $reflection;


    /** 
     * here we hold function that has #[QLQuery] Attribute
     * @var array
     */
    private array $queries = [];


    private array $mutations = [];

    public function getFields(): array
    {
        $props = $this->reflection->getProperties();
        $fields = [];
        $class = $this->reflection->getName();
        foreach ($props as $prop) {
            // here we set property name as a field of ObjectType
            /** @var ReflectionProperty $prop */
            $fields[$prop->getName()] = [
                'type' => Util::resolveType($prop->getType(), $prop->getAttributes(), $prop->getName(), $class),
                'resolve' => static function ($rootVal, $args) {
                    dd("HELLO IN DTO RESOLVE");
                }
            ];
        }
        return $fields;
    }




    public function generateQuires(): void
    {
        $methods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, function (ReflectionMethod $method) {
            $attributes = $method->getAttributes(QLQuery::class);
            return count($attributes) > 0;
        });

        $this->queries = $this->generate($methods);
    }

    public function generateMutations()
    {
        $methods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $methods = array_filter($methods, function (ReflectionMethod $method) {
            $attributes = $method->getAttributes(QLMutation::class);
            return count($attributes) > 0;
        });

        $this->mutations = $this->generate($methods);
    }

    private function generate(array $methods)
    {
        $class = $this->reflection->getName();
        $final = [];
        foreach ($methods as $method) {
            if (!$method->hasReturnType()) {
                throw new QueryMustHaveReturnTypeException("You must define a `return type` for $method->name in $class");
            }

            $type = Util::resolveType($method->getReturnType(), $method->getAttributes(), $method->getName(), $class);
            $methodName = $method->getName();
            $modelClassName = $this->reflection->getName();
            $args = Util::getQueryArgs($method);

            $final[$methodName] = [
                'type' => $type,
                'resolve' => static function ($rootVal, $args) use ($modelClassName, $methodName, $method, $type) {
                    try {

                        //TODO we must create a debug situation to log resolvers
                        $object = resolve($modelClassName);
                        return call_user_func_array([$object, $methodName], $args);
                    } catch (ArgumentCountError $error) {
                        $parameters = $method->getParameters();
                        foreach ($parameters as $parameter) {
                            $name = $parameter->getName();
                            if (!key_exists($name, $args)) {
                                //TODO this Exception must change and must return a error in the final result instead of Internal Error message 
                                throw new RuntimeException(
                                    "Field \"$methodName\" argument \"$name\" of type \"{$type->toString()}\" is required but not provided. "
                                );
                            }
                        }
                        return FormattedError::createFromException($error);
                    }
                },
                'args' => $args
            ];
        }
        return $final;
    }

    public function getQueries(): array
    {
        return $this->queries;
    }

    public function getMutations(): array
    {
        return $this->mutations;
    }
}
