<?php

namespace Kpacha\Datastructure\Tree\Binary;

/**
 * Simple BalancedBinarySearchTree
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BalancedBinarySearchTree extends BinarySearchTree
{

    /**
     * balance the tree in order to improve searchs
     */
    public function balance()
    {
        $items = $this->dump();
        $this->prune();
        $this->insertBalanced($items);
    }

    /**
     * balance the sorted items and insert them
     * @param array $items
     */
    public function insertBalanced($items)
    {
        if (!empty($items)) {
            $chunks = $this->splitItems($items);
            $centerNode = array_pop($chunks[0]);

            $this->insert($centerNode);
            $this->insertBalanced($chunks[0]);
            if (isset($chunks[1])) {
                $this->insertBalanced($chunks[1]);
            }
        }
    }

    protected function splitItems($items)
    {
        return array_chunk($items, ceil(count($items) / 2));
    }

}
