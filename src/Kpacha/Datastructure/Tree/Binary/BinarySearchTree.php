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
            if ($item > $subtree->value) {
                $this->insertItem($item, $subtree->right);
            } else if ($item < $subtree->value) {
                $this->insertItem($item, $subtree->left);
            }
        }
    }

    /**
     * pre-order traverse for node deletion
     * @param mixed $node
     * @param BinaryNode $subtree
     */
    protected function removeItem($item, &$subtree)
    {
        if ($item === $subtree->value) {
            $this->removeSubtreeRoot($subtree);
        } else {
            $side = ($item > $subtree->value) ? 'right' : 'left';
            $this->removeItem($item, $subtree->$side);
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
            $this->removeItem($mostLeftLeaftOnRightSubtree->value, $subtree->right);
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
