<?php


namespace LaravelQL\LaravelQL;

use GraphQL\Type\Definition\ListOfType;
use ReflectionNamedType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ScalarType;
use GraphQL\Type\Definition\NonNull;
use LaravelQL\LaravelQL\Core\Attributes\QLArray;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\ListNeedsQLArrayAttributeException;
use LaravelQL\LaravelQL\Exceptions\NestedArrayNotAllowedException;
use ReflectionUnionType;
use LaravelQL\LaravelQL\Exceptions\UnionNotAllowedAsFieldType;
use ReflectionIntersectionType;
use ReflectionAttribute;
use ReflectionMethod;

class Util
{
    public static function getLazyType(string $typeName, bool $allowsNull): callable
    {
        $handler = QLHandler::getInstance();
        return static fn() => $handler->{$typeName}($allowsNull);
    }

    /**
     * Undocumented function
     *
     * @param ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null $type
     * @param ReflectionAttribute<T>[] $attributes
     * @param string $name
     * @param string $class
     * @return mixed
     */
    public static function resolveType(
        ReflectionNamedType|ReflectionUnionType|ReflectionIntersectionType|null|string $type,
        array $attributes,
        string $name,
        string $class,
    ): callable|ScalarType|NonNull|ListOfType|null {


        if ($type === null) {
            return null;
        }

        if ($type instanceof ReflectionUnionType) {
            throw new UnionNotAllowedAsFieldType("The $name in $class can't be union");
        }

        // $typeName = is_string($type) ? $type : $type->getName();
        if (is_string($type)) {
            $typeName = $type;
            $allowsNUll = true; //it's just for handling the array , the innerType allowed null handles in the handleArrayType function , after returning the value
        } else {
            $typeName = $type->getName();
            $allowsNUll = $type->allowsNull();
        }

        $theType =  match ($typeName) {
            'string' => Type::string(),
            'int' => Type::int(),
            'float' => Type::float(),
            'boolean' => Type::boolean(),
            'array' => self::handleArrayType($attributes,  $name, $class),
            'mixed' => throw new InvalidReturnTypeException("The `mixed` return type not allowed in query $name in $class"),
                // actually it's a custom type if it isn't above types
            default => self::getLazyType($typeName, $allowsNUll)
        };

        if (is_callable($theType)) {
            return $theType;
        }

        return $allowsNUll ? $theType : Type::nonNull($theType);
    }


    private static function handleArrayType(
        array $attributes,
        string $name,
        string $class
    ): ListOfType|NonNull {
        $filteredAttributes = array_filter($attributes, fn($attr) => $attr->getName() == QLArray::class);

        if (empty($filteredAttributes)) {
            throw new ListNeedsQLArrayAttributeException("The $name in $class needs a QLArray Attribute");
        }

        $filteredAttributes = array_values($filteredAttributes); // it's just simply rearrange the array to access the 0 index if there is multi attribute
        $qlArrayArgs = $filteredAttributes[0]->getArguments();
        $innerType = $qlArrayArgs[0];

        $innerTypeAllowsNull = isset($qlArrayArgs[1]) ? $qlArrayArgs[1] : false;

        if ($innerType == "array") {
            // it's break the recursive call of this function
            throw new NestedArrayNotAllowedException("You can't pass array as value for #[QLArray]");
        }

        $innerType = self::resolveType($innerType, $attributes, $name, $class);
        $innerType = $innerTypeAllowsNull ? $innerType : Type::nonNull($innerType);

        // $typeList = Type::listOf($innerType);

        // dd($typeList);
        // means if the type can be null pass like this [String] , else pass like this [String]!
        // some time they can Combine like this [String!] or [String!]! , this happens when the inner type is nonNull
        return Type::listOf($innerType);
    }


    public static function getQueryArgs(ReflectionMethod $method)
    {
        $args = [];
        foreach ($method->getParameters() as $param) {

            /** @var ReflectionParameter $param */
            $paramName = $param->getName();
            $args[$paramName] = [
                'type' => Util::resolveType($param->getType(), [], "$paramName argument ", $method->class),
            ];

            if ($param->isDefaultValueAvailable()) {
                $args[$paramName]['defaultValue'] = $param->getDefaultValue();
            }
        }
        return $args;
    }
}
