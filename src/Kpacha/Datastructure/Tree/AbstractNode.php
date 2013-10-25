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
}
