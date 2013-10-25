<?php

namespace Kpacha\Datastructure\Tree;

/**
 * Abstrcat Node
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

    public function dump(\SplQueue $queue)
    {
        $queue->enqueue($this->value);
        return $queue;
    }

    abstract public function search($item);

    abstract public function prune();

}
