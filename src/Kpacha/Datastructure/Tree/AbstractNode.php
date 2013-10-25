<?php

namespace Kpacha\Datastructure\Tree;

/**
 * Abstract Node
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
abstract class AbstractNode
{

    public $value;

    public function __construct($item)
    {
        $this->value = $item;
    }

    abstract public function dump(\SplQueue $queue);

    abstract public function hasChildren();

    abstract public function search($item);

    abstract public function prune();

    abstract public function getDepth();

    /**
     * Ask a subtree for its depth
     * @param AbstractNode $child
     * @return int
     */
    protected function getChildDepth($child)
    {
        return ($child !== null) ? $child->getDepth() : 0;
    }

}
