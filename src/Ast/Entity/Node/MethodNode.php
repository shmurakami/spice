<?php

namespace shmurakami\Spice\Ast\Entity\Node;

use ast\Node;
use shmurakami\Spice\Ast\Context\Context;
use shmurakami\Spice\Ast\Parser\DocCommentParser;
use shmurakami\Spice\Stub\Kind;

class MethodNode
{
    use DocCommentParser;

    /**
     * @var string
     */
    private $namespace;
    /**
     * @var Node
     */
    private $node;
    /**
     * @var Context
     */
    private $context;

    public function __construct(Context $context, Node $node)
    {
        $this->context = $context;
        $this->namespace = $context->extractNamespace();
        $this->node = $node;
    }

    /**
     * @return Context[]
     */
    public function parseMethodAttributeToContexts()
    {
        $dependencyContexts = [];

        // doc comment
        $doComment = $this->node->children['docComment'] ?? '';
        $contexts = $this->parseDocComment($this->context, $doComment, '@param');
        foreach ($contexts as $context) {
            $dependencyContexts[] = $context;
        }

        // type hinting
        // consider nullable type?
        $argumentNodes = $this->node->children['params']->children ?? [];
        $typeNames = array_map(function (Node $node) {
            return $node->children['type']->children['name'] ?? '';
        }, $argumentNodes);
        foreach ($typeNames as $typeName) {
            if ($typeName) {
                $context = $this->toContext($this->namespace, $typeName);
                if ($context) {
                    $dependencyContexts[] = $context;
                }
            }
        }

        // return statement
        // return type
        $returnTypeNode = $this->node->children['returnType'] ?? null;
        if ($returnTypeNode
            && ($returnTypeNode->kind === Kind::AST_TYPE || $returnTypeNode->kind === Kind::AST_NULLABLE_TYPE)
        ) {
            $typeName = $returnTypeNode->children['type']->children['name'] ?? '';
            if ($typeName) {
                $context = $this->toContext($this->namespace, $typeName);
                if ($context) {
                    $dependencyContexts[] = $context;
                }
            }
        }

        // return type in doc comment
        // redundant to parse doc comment again?
        $doComment = $this->node->children['docComment'] ?? '';
        $contexts = $this->parseDocComment($this->context, $doComment, '@return');
        foreach ($contexts as $context) {
            $dependencyContexts[] = $context;
        }

        $uniqueDependencyContexts = [];
        foreach ($dependencyContexts as $context) {
            $fqcn = $context->fqcn();
            if (isset($uniqueDependencyContexts[$fqcn])) {
                continue;
            }
            $uniqueDependencyContexts[$fqcn] = $context;
        }
        // fqcn key is not needed
        return array_values($uniqueDependencyContexts);
    }

}
