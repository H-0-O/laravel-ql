<?php

namespace LaravelQL\LaravelQL\Core\Attributes;


use Attribute;
use LaravelQL\LaravelQL\Util;
use ReflectionClass;
use ReflectionProperty;

#[Attribute]
class QLDTO
{
    public ReflectionClass $reflection;


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
}
