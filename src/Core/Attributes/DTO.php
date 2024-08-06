<?php

namespace LaravelQL\LaravelQL\Core\Attributes;


use Attribute;
use GraphQL\Type\Definition\Type;
use ReflectionClass;
use ReflectionProperty;
use ReflectionType;
use stdClass;

#[Attribute]
class DTO
{
    private ReflectionClass $reflection;
    private array $properties;

    /**
     * @throws \ReflectionException
     */
    public function __construct(
        string $dtoClass = null
    ) {
        $this->reflection = new ReflectionClass($dtoClass);
        $this->setProperties();
    }


    private function setProperties(): void
    {
        $props = $this->reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $props = array_filter($props, static function ($property) {
            $hasSkip = $property->getAttributes(Skip::class);
            return count($hasSkip) === 0; //means if the property doesn't use the Skip flag
        });

        array_map(static function($property){
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
                            $types?->allowsNull());
                    }else{
                        dd($type);
                    }
                }
            }
            dd("NANO" , $final);
            $arr = [
              $property->name => [
                    'type' => $types
              ]
            ];

        } , $props);
//        dd($this->properties);
    }
    public function getProperties(){
        return $this->properties;
    }

    private static function getBuiltInType(string $type , $allowedNull): mixed{
        $theType =  match ($type) {
            'string' => Type::string(),
            'int' => Type::int(),
            'float' => Type::float(),
            'boolean' => Type::boolean(),
        };
        return $allowedNull ? $theType : Type::nonNull($theType);
    }

    private static function getCustomType($typeName , $allowedNull){

    }
}
