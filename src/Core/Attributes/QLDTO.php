<?php

namespace LaravelQL\LaravelQL\Core\Attributes;


use Attribute;
use GraphQL\Type\Definition\NonNull;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\Type;
use LaravelQL\LaravelQL\Exceptions\UnionNotAllowedAsFieldType;
use LaravelQL\LaravelQL\QLHandler;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;

#[Attribute]
class QLDTO
{
    public ReflectionClass $reflection;


    public function getFields(): array
    {
        $props = $this->reflection->getProperties();
        $fields = [];

        foreach ($props as $prop) {
            // here we set property name as a field of ObjectType
            /** @var ReflectionProperty $prop */
            $fields[$prop->getName()] = $this->getTypeAndResolve($prop);
        }
        return $fields;
    }

    private function getTypeAndResolve(ReflectionProperty $prop): array
    {
        $type = $prop->getType();

        if ($type instanceof ReflectionUnionType) {
            throw new UnionNotAllowedAsFieldType("The {$prop->getName()} in {$prop->class} can't be union");
        }


        if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
            return [
                'type' => $this->getBuiltInType($type->getName(), $type->allowsNull()),
                'resolve' => fn() => 'I need dynamic resolver'
            ];
        }




        if ($type instanceof ReflectionNamedType) {

            $lazyLoadingType = $this->getLazyType($type);
            return [
                'type' => $lazyLoadingType,
                'resolve' => fn() => ''
            ];
        }
    }


    private function getBuiltInType(string $type, $allowedNull): ScalarType|NonNull
    {
        $theType =  match ($type) {
            'string' => Type::string(),
            'int' => Type::int(),
            'float' => Type::float(),
            'boolean' => Type::boolean(),
        };
        return $allowedNull ? $theType : Type::nonNull($theType);
    }

    private function getLazyType(ReflectionNamedType $type): callable
    {
        $handler = QLHandler::getInstance();
        return static fn() => $handler->{$type->getName()}($type->allowsNull());
    }
}
