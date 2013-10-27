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

    public function __construct($minRange = self::MIN_RANGE)
    {
        parent::__construct();
        $this->minRange = $minRange;
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
                $subtree->value[$item->key] = $item;
                usort($subtree->value, array('\Kpacha\Datastructure\Index', 'compare'));
                if (count($subtree->value) > 2 * $this->minRange) {
                    $subtree->split();
                }
            } else {
                $this->insertItem($item, $subtree->getSubNodeWhereInsert($item));
            }
        }
    }

    protected function removeItem($item, &$subtree)
    {
        throw new Exception('Unimplemented method!');
    }

}
