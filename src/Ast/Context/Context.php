<?php

namespace shmurakami\Spice\Ast\Context;

class Context implements ContextInterface
{
    use ContextBehavior;

    /**
     * @var string
     */
    private $fqcn;

    /**
     * Context constructor.
     */
    public function __construct(string $fqcn)
    {
        $this->fqcn = $fqcn;
        $this->extractNamespaceAndClass($fqcn);
    }

    public function fqcn(): string
    {
        // add \\ prefix if global namespace
        return $this->namespace . '\\' . $this->className;
    }
}