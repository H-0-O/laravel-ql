<?php

namespace LaravelQL\LaravelQL\Core\Attributes;


use Attribute;
use GraphQL\Type\Definition\Type;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;

#[Attribute]
class QLDTO
{
    public ReflectionClass $reflection;

    private array $properties;

    public function getFields(): array
    {
        $props = $this->reflection->getProperties();
        $fields = [];

        foreach ($props as $prop) {
            // here we set property name as a field of ObjectType
            /** @var ReflectionProperty $prop */
            $fields[$prop->getName()] = [];
            $type = $this->getPropertyType($prop);
        }
        dd($props);
        return [];
    }

    private function getPropertyType(ReflectionProperty $prop)
    {
        $type = $prop->getType();
        if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
            return $this->getBuiltInType($type->getName(), $type->allowsNull());
        }
        dd($type);
    }

    private function setProperties(): void
    {
        $props = $this->reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $props = array_filter($props, static function ($property) {
            $hasSkip = $property->getAttributes(Skip::class);
            return count($hasSkip) === 0; //means if the property doesn't use the Skip flag
        });

        array_map(static function ($property) {
            /** @var ReflectionProperty $property */
            $types = $property->getType();
            $final = [];
            if (method_exists($types, "getTypes")) { //it's a union
                foreach ($types?->getTypes() as $type) {
                    /** @var ReflectionType $type */
                    //                    dd($type)
                    if ($type->isBuiltin() && $type->getName() !== 'null') {
                        $final[$property->name]['types'][] = self::getBuiltInType(
                            $type->getName(),
                            $types?->allowsNull()
                        );
                    } else {
                        self::getCustomType($type->getName(), $type->allowsNull());
                    }
                }
            }
            dd("NANO", $final);
            $arr = [
                $property->name => [
                    'type' => $types
                ]
            ];
        }, $props);
        //        dd($this->properties);
    }


    private function getBuiltInType(string $type, $allowedNull): mixed
    {
        $theType =  match ($type) {
            'string' => Type::string(),
            'int' => Type::int(),
            'float' => Type::float(),
            'boolean' => Type::boolean(),
        };
        return $allowedNull ? $theType : Type::nonNull($theType);
    }

    private static function getCustomType($typeName, $allowedNull)
    {
        $re = &QLContainer::getCustomType($typeName);
        $ww = QLContainer::$types[$typeName]->ql;
        dd($re, $ww);
    }
}
