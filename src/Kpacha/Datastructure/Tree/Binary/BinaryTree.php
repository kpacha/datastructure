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
     * @param type $node
     * @param type $subtree
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

}
