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
     * does the node have any children?
     * @return boolean
     */
    public function hasChildren()
    {
        return $this->right !== null || $this->left !== null;
    }

    /**
     * does the node have just one child?
     * @return BinaryNode | false
     */
    public function getChild()
    {
        $child = false;
        if ($this->right !== null && $this->left === null) {
            $child = $this->right;
        } else if ($this->right === null && $this->left !== null) {
            $child = $this->left;
        }
        return $child;
    }

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

    public function getDepth()
    {
        $leftTree = $this->getChildDepth($this->left);
        $rightTree = $this->getChildDepth($this->right);
        return 1 + max(array($leftTree, $rightTree));
    }

}
