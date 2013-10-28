<?php

namespace Kpacha\Datastructure\Tree\Nary;

use Kpacha\Datastructure\Tree\AbstractTree;

/**
 * Simple implementation of a BTree
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BTree extends AbstractTree
{

    const MIN_RANGE = 1;

    protected $minRange;
    protected $maxRange;

    public function __construct($minRange = self::MIN_RANGE)
    {
        parent::__construct();
        $this->minRange = $minRange;
        $this->maxRange = 2 * $minRange;
    }

    protected function createNode($item, $parentNode = null)
    {
        return new BNode($item, $parentNode, $this->minRange);
    }

    protected function insertItem($item, &$subtree, &$parentNode = null)
    {
        if ($subtree === null) {
            $subtree = $this->createNode($item, $parentNode);
        } else {
            if (!$subtree->hasChildren()) {
                $subtree->insertItems($item);
                $this->check($subtree, $parentNode);
            } else {
                $subNode = $subtree->getSubNodeWhereInsert($item);
                $this->insertItem($item, $subNode, $subtree);
            }
        }
    }

    protected function removeItem($item, &$subtree)
    {
        throw new Exception('Unimplemented method!');
    }

    /**
     * if the node has more keys than (2 * minRange), split it!
     * @param BNode $subtree
     * @param BNode $formerRoot
     */
    protected function check(&$subtree, &$formerRoot)
    {
        if (count($subtree->value) > $this->maxRange) {
            $subtree->split();
            if ($formerRoot) {
                $this->merge($subtree, $formerRoot);
            }
        }
    }

    protected function merge(&$subject, &$root)
    {
        if (count($root->value) < $this->maxRange) {
            $root->removeSubNode($subject);
            $root->insertItems($subject->value);
            $subNodes = $subject->getSubNodes();
            foreach ($subNodes as $key => $subtree) {
                $subtree->setParent($root);
                $root->setSubNode($key, $subtree);
            }
            $root->fixRangeKeys();
        }
    }

}
