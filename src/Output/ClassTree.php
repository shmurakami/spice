<?php

namespace shmurakami\Spice\Output;

class ClassTree
{
    /**
     * @var ClassTreeNode
     */
    private $rootNode;
    /**
     * @var ClassTree[]
     */
    private $childTree = [];

    /**
     * ClassTree constructor.
     */
    public function __construct(ClassTreeNode $rootNode)
    {
        $this->rootNode = $rootNode;
    }

    public function add(ClassTree $classTree)
    {
        $this->childTree[] = $classTree;
    }

    public function getRootNode(): ClassTreeNode
    {
        return $this->rootNode;
    }

    public function getRootNodeClassName(): string
    {
        return $this->rootNode->getClassName();
    }

    /**
     * @return ClassTreeNode[]
     */
    public function getChildNodes(): array
    {
        return array_map(function (ClassTree $classTree) {
            return $classTree->getRootNode();
        }, $this->childTree);
    }

    /**
     * @return ClassTree[]
     */
    public function getChildTrees(): array
    {
        return $this->childTree;
    }

    /**
     * @TODO delete it. this is just for test
     * @return string[]
     */
    public function toArray(array $nodes = []): array
    {
        $childNodes = [];
        foreach ($this->childTree as $childNode) {
            $childNodes[] = $childNode->toArray($nodes);
        }
        return [
            'className'  => $this->rootNode->getClassName(),
            'childNodes' => $childNodes,
        ];
    }
}
