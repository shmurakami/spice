<?php

namespace shmurakami\Spice\Output;

class LazyReplacementTree implements ObjectRelationTree
{

    /**
     * @var string
     */
    private $className;

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return string
     */
    public function nameShouldBeReplaced(): string
    {
        return $this->className;
    }

    public function shouldBeReplacedBy(string $className): bool
    {
        return $this->className === $className;
    }

    public function getChildTrees(): array
    {
        return [];
    }

    public function replacementTree(): ObjectRelationTree
    {
        return $this;
    }

    public function add(ObjectRelationTree $classTree)
    {
    }
}
