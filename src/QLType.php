<?php

namespace LaravelQL\LaravelQL;

use GraphQL\Type\Definition\ObjectType;
use LaravelQL\LaravelQL\Core\Attributes\QLDTO;
use LaravelQL\LaravelQL\Core\Attributes\QLModel;
use LaravelQL\LaravelQL\Exceptions\DTOAttributeMissing;
use LaravelQL\LaravelQL\Exceptions\DTOPathIsEmpty;
use ReflectionClass;

class QLType
{

    private QLModel $QLModel;

    private QLDTO $QLDTO;

    private ObjectType|null $objectType = null;

    public function __construct(private string $modelPath) {}



    public function initQLModel(): bool
    {

        $qlModelReflection = new ReflectionClass($this->modelPath);

        $attributes = $qlModelReflection->getAttributes(QLModel::class);

        if (count($attributes) == 0) {
            return false;
        }

        $this->QLModel = new QLModel();
        $this->QLModel->reflection = $qlModelReflection;
        $this->QLModel->typeName = $qlModelReflection->getShortName();
        $this->QLModel->typeNameWithPath = $qlModelReflection->getName();

        return true;
    }


    public function initQLDTO()
    {
        $qlModelAttributes = $this->QLModel->reflection->getAttributes(QLModel::class);
        $dtoPath = $qlModelAttributes[0]->getArguments()[0];

        if (empty($dtoPath)) {
            throw new DTOPathIsEmpty("DTO path can't be empty ");
        }

        $qlDTOReflection = new ReflectionClass($dtoPath);
        $dtoAttributes = $qlDTOReflection->getAttributes(QLDTO::class);

        if (count($dtoAttributes) == 0) {
            throw new DTOAttributeMissing("The $dtoPath need #[QLDTO] attribute");
        }

        $this->QLDTO = new QLDTO();
        $this->QLDTO->reflection = $qlDTOReflection;
    }


    public function initObjectType()
    {
        $config = [
            'name' => $this->getTypeName(),
            'fields' => $this->QLDTO->getFields()
        ];
    }

    /**
     * we use the this for internal 
     *
     * @return string
     */
    public function getTypeName(): string
    {
        return $this->QLModel->typeName;
    }
}