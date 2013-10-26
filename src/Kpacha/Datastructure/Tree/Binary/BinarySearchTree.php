<?php

namespace Kpacha\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\AbstractTree;

/**
 * Simple BinarySearchTree
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinarySearchTree extends AbstractTree
{

    protected function createNode($item)
    {
        return new BinaryNode($item);
    }

    /**
     * pre-order traverse for insertion
     * @param mixed $item
     * @param BinaryNode $subtree
     */
    protected function insertItem($item, &$subtree)
    {
        if ($subtree === null) {
            $subtree = $this->createNode($item);
        } else {
            $compResult = $item->compareWith($subtree->value);
            if ($compResult == 1) {
                $this->insertItem($item, $subtree->right);
            } else if ($compResult == -1) {
                $this->insertItem($item, $subtree->left);
            }
        }
    }

    /**
     * pre-order traverse for node deletion
     * @param mixed $key
     * @param BinaryNode $subtree
     */
    protected function removeItem($key, &$subtree)
    {
        if ($key === $subtree->value->key) {
            $this->removeSubtreeRoot($subtree);
        } else {
            $side = ($key > $subtree->value->key) ? 'right' : 'left';
            $this->removeItem($key, $subtree->$side);
        }
    }

    /**
     * Remove the root node and fix the tree structure
     * @param BinaryNode $subtree
     */
    protected function removeSubtreeRoot(&$subtree)
    {
        if ($subtree->hasChildren()) {
            $this->promoteChildToRoot($subtree);
        } else {
            $subtree = null;
        }
    }

    /**
     * Find the best candidate for the promotion and move it to the root
     * @param BinaryNode $subtree
     */
    protected function promoteChildToRoot(&$subtree)
    {
        if (($child = $subtree->getChild()) !== false) {
            $subtree = $child;
        } else {
            $mostLeftLeaftOnRightSubtree = $this->getMostLeftLeaf($subtree->right);
            $subtree->value = $mostLeftLeaftOnRightSubtree->value;
            $this->removeItem($mostLeftLeaftOnRightSubtree->value->key, $subtree->right);
        }
    }

    /**
     * Get the most left leaf from the subtree
     * @param BinaryNode $subtree
     * @return BinaryNode
     */
    protected function getMostLeftLeaf(&$subtree)
    {
        $mostLeftLeaft = &$subtree;
        while ($mostLeftLeaft->left != null) {
            $mostLeftLeaft = &$mostLeftLeaft->left;
        }
        return $mostLeftLeaft;
    }

}
