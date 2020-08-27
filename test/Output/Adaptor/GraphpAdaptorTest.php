<?php

namespace shmurakami\Spice\Test\Output\Adaptor;

use shmurakami\Spice\Ast\Context\ClassContext;
use shmurakami\Spice\Output\Adaptor\AdaptorConfig;
use shmurakami\Spice\Output\Adaptor\GraphpAdaptor;
use shmurakami\Spice\Output\ClassTree;
use shmurakami\Spice\Output\ClassTreeNode;
use shmurakami\Spice\Test\TestCase;

class GraphpAdaptorTest extends TestCase
{

    public function testBuildGraph()
    {
        // 3 levels
        $parentTree = new ClassTree(new ClassTreeNode(new ClassContext('Foo')));
        $child1 = new ClassTree(new ClassTreeNode(new ClassContext('Bar')));
        $child2 = new ClassTree(new ClassTreeNode(new ClassContext('Baz')));
        $grandChild1 = new ClassTree(new ClassTreeNode(new ClassContext('FooBarFoo')));
        $grandChild2 = new ClassTree(new ClassTreeNode(new ClassContext('FooBarBar')));
        $grandChild3 = new ClassTree(new ClassTreeNode(new ClassContext('FooBazFoo')));
        $grandChild4 = new ClassTree(new ClassTreeNode(new ClassContext('FooBazBaz')));

        $child1->add($grandChild1);
        $child1->add($grandChild2);
        $child2->add($grandChild3);
        $child2->add($grandChild4);

        $parentTree->add($child1);
        $parentTree->add($child2);

        $adaptor = new GraphpAdaptor(new AdaptorConfig(''));
        $graph = $adaptor->buildGraph($parentTree);

        // sum of parent, child, grandchild
        $this->assertEquals(7, count($graph->getVertices()));
        // sum of edges between parent and child, child and grandchild
        $this->assertEquals(6, count($graph->getEdges()));
    }
}
