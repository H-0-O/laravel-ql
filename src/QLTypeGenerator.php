<?php

namespace LaravelQL\LaravelQL;

use GraphQL\Utils\BuildSchema;
use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\QLModel;
use LaravelQL\LaravelQL\Core\QLType;
use ReflectionClass;
use ReflectionException;

class QLTypeGenerator
{
    private const QL_NAME = "QL_Name";
    private Model $model;

    private ReflectionClass $reflection;

    private QLType $type;

    /**
     * @throws ReflectionException
     */
    public function __construct(
        private string $modelPath
    ) {
        $this->model = new $this->modelPath();
        $this->reflection = new ReflectionClass($this->model);
        $this->generate();
    }

    private function generate()
    {
        $attrs = $this->reflection->getAttributes(QLModel::class);
        if (count($attrs) === 0) {
            return;
        }

        $this->type = new QLType();
        $this->setName();
    }

    private function setName()
    {
        $constants = $this->reflection->getConstants();
        $this->type->name = ucfirst($constants[self::QL_NAME] ?? $this->reflection->getShortName());
    }


}
