<?php

namespace LaravelQL\LaravelQL;

use Illuminate\Database\Eloquent\Model;
use LaravelQL\LaravelQL\Core\QLModel;
use LaravelQL\LaravelQL\Exceptions\InvalidReturnTypeException;
use LaravelQL\LaravelQL\Exceptions\QueryMustHaveReturnTypeException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class QLGenerator
{

    private ReflectionClass $reflection;

    /**
     * @var array<ReflectionMethod>
     */
    private array $QLModelMethods = [];
    private QLModel $QLModel;

    /**
     * @throws ReflectionException
     */
    public function __construct(
        string $modelPath
    ) {
        $this->reflection = new ReflectionClass(
            new $modelPath()
        );
    }

    public function createBaseConf(): bool
    {

        $attrs = $this->reflection->getAttributes(QLModel::class);
        if (count($attrs) === 0) {
            return false;
        }

        $this->QLModel = new QLModel();
        //TODO later here must check that user enter custom name or no
        $this->QLModel->typeName = $this->reflection->getShortName();
        return true;
    }

    /**
     * @throws QueryMustHaveReturnTypeException
     * @throws InvalidReturnTypeException
     */
    public function generate(): void
    {
        $this->extractDataOnce();
        $this->extractQueriesAndMutations();


        $this->QLModel->buildQuires();
        $this->QLModel->buildMutations();
    }

    private function extractDataOnce(): void
    {
        $this->QLModelMethods = $this->reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    private function extractQueriesAndMutations(): void
    {
        /** @var ReflectionMethod $modelMethod */
        foreach ($this->QLModelMethods as $modelMethod) {
            $name = $modelMethod->getName();
            if (str_starts_with($name, "query") && $name !== "query") {
                $this->QLModel->addQueryMethod($modelMethod);
            } elseif (str_starts_with($name, "mut") && $name !== "mut" ) {
                $this->QLModel->addMutationMethod($modelMethod);
            }
        }
    }

    public function getQLModelName(): string
    {
        return $this->QLModel->typeName;
    }
}
