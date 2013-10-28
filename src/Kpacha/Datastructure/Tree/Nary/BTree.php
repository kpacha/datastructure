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

    protected function createNode($item)
    {
        return new BNode($item, $this->minRange);
    }

    protected function insertItem($item, &$subtree)
    {
        if ($subtree === null) {
            $subtree = $this->createNode($item);
        } else {
            if (!$subtree->hasChildren()) {
                $subtree->insertItems($item);
                $subtree->check();
            } else {
                $subNode = $subtree->getSubNodeWhereInsert($item);
                $this->insertItem($item, $subNode);
            }
        }
    }

    protected function removeItem($item, &$subtree)
    {
        throw new Exception('Unimplemented method!');
    }

}
