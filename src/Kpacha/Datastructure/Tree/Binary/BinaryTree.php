<?php

namespace Kpacha\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\AbstractTree;

/**
 * Simple BinaryTree
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinaryTree extends AbstractTree
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
            } else {
                // reject duplicates
            }
        }
    }

    /**
     * pre-order traverse for insertion
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
        if ($subtree->left === null && $subtree->right === null) {
            $subtree = null;
        } else {
            if ($subtree->right !== null && $subtree->left === null) {
                $subtree = $subtree->right;
            } else if ($subtree->left !== null && $subtree->right === null) {
                $subtree = $subtree->left;
            } else {
                $mostLeftLeaftOnRightSubtree = $this->getMostLeftLeaf($subtree->right);
                $subtree->value = $mostLeftLeaftOnRightSubtree->value;
                $this->removeNode($mostLeftLeaftOnRightSubtree, $subtree->right);
            }
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
