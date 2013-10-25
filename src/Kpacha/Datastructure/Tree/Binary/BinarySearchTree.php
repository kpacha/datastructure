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
     * @param BinaryNode $node
     * @param BinaryNode $subtree
     */
    protected function insertNode($node, &$subtree)
    {
        if ($subtree === null) {
            $subtree = $node;
        } else {
            if ($node->value > $subtree->value) {
                $this->insertNode($node, $subtree->right);
            } else if ($node->value < $subtree->value) {
                $this->insertNode($node, $subtree->left);
            }
        }
    }

    /**
     * pre-order traverse for node deletion
     * @param BinaryNode $node
     * @param BinaryNode $subtree
     */
    protected function removeNode($node, &$subtree)
    {
        if ($node->value === $subtree->value) {
            $this->removeSubtreeRoot($subtree);
        } else if ($node->value > $subtree->value) {
            $this->removeNode($node, $subtree->right);
        } else {
            $this->removeNode($node, $subtree->left);
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
            $this->removeNode($mostLeftLeaftOnRightSubtree, $subtree->right);
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
