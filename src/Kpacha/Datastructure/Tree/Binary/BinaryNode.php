<?php

namespace Kpacha\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\AbstractNode;

/**
 * Simple BinaryNode
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinaryNode extends AbstractNode
{

    /**
     * @var BinaryNode 
     */
    public $left = null;

    /**
     * @var BinaryNode 
     */
    public $right = null;

    /**
     * in-order dump
     */
    public function dump(\SplQueue $queue)
    {
        if ($this->left !== null) {
            $queue = $this->left->dump($queue);
        }
        $queue->enqueue($this->value);
        if ($this->right !== null) {
            $queue = $this->right->dump($queue);
        }
        return $queue;
    }

    /**
     * post-order prune
     */
    public function prune()
    {
        if ($this->left !== null) {
            $this->left->prune();
        }
        if ($this->right !== null) {
            $this->right->prune();
        }
        $this->value = null;
    }

    /**
     * pre-order search
     * @param type $item
     */
    public function search($item)
    {
        $node = null;
        if ($this->value == $item) {
            $node = $this;
        } else if ($this->left !== null) {
            $node = $this->left->search($item);
        }
        if ($node === null && $this->right !== null) {
            $node = $this->right->search($item);
        }
        return $node;
    }

}
