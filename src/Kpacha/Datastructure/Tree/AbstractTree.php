<?php

namespace Kpacha\Datastructure\Tree;

/**
 * Abstract class for trees
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
abstract class AbstractTree
{

    /**
     * @var AbstractNode
     */
    protected $root;

    public function __construct()
    {
        $this->root = null;
    }

    public function isEmpty()
    {
        return $this->root === null;
    }

    /**
     * insert the item into the tree
     * @param mixed $item
     */
    public function insert($item)
    {
        $this->insertItem($item, $this->root);
    }

    /**
     * @return AbstractNode
     */
    abstract protected function createNode($item);

    abstract protected function insertItem($node, &$subtree);

    /**
     * dump all the tree
     */
    public function dump()
    {
        $values = array();
        if (!$this->isEmpty()) {
            $queue = $this->root->dump(new \SplQueue());
            while (!$queue->isEmpty()) {
                $values[] = $queue->dequeue();
            }
        }
        return $values;
    }

    /**
     * search for the node with the received value
     * @param mixed $item
     * @return AsbtractNode
     */
    public function search($item)
    {
        $node = null;
        if (!$this->isEmpty()) {
            $node = $this->root->search($item);
        }
        return $node;
    }

    /**
     * delete all the tree structure
     */
    public function prune()
    {
        if (!$this->isEmpty()) {
            $this->root->prune();
            $this->root = null;
        }
    }

    public function remove($key)
    {
        if (!$this->isEmpty()) {
            $this->removeItem($key, $this->root);
        }
    }

    abstract protected function removeItem($key, &$subtree);

    /**
     * get the depth of the tree
     * @return int
     */
    public function getDepth()
    {
        return ($this->isEmpty()) ? 0 : $this->root->getDepth() - 1;
    }

}
