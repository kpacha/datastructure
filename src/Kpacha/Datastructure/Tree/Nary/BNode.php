<?php

namespace Kpacha\Datastructure\Tree\Nary;

use Kpacha\Datastructure\Tree\AbstractNode;
use \MultipleIterator;
use \ArrayIterator;

/**
 * Simple implementation of BNode
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BNode extends AbstractNode
{

    const MIN_RANGE = 1;

    protected $minRange;
    protected $maxRange;
    protected $subNodes = array();
    protected $parent = null;

    public function __construct($items, $minRange = self::MIN_RANGE)
    {
        parent::__construct((is_array($items) ? $items : array($items->key => $items)));
        $this->minRange = $minRange;
        $this->maxRange = 2 * $minRange;
    }

    /**
     * in-order traverse for dumping the indexed items
     * @param \SplQueue $queue
     * @return \SplQueue
     */
    public function dump(\SplQueue $queue)
    {
        $multiIterator = $this->getMultipleIterator();

        foreach ($multiIterator as $pair) {
            $queue = $this->dumpChild($pair, $queue);
            $queue = $this->dumpKey($pair, $queue);
        }
        return $queue;
    }

    protected function getMultipleIterator()
    {
        $multiIterator = new MultipleIterator(MultipleIterator::MIT_NEED_ANY | MultipleIterator::MIT_KEYS_ASSOC);
        $multiIterator->attachIterator(new ArrayIterator(($this->value)? : array()), 'keys');
        $multiIterator->attachIterator(new ArrayIterator(($this->subNodes)? : array()), 'subNodes');
        return $multiIterator;
    }

    /**
     * dump the child node into the queue if it is set
     * @param array $pair
     * @param \SplQueue $queue
     * @return \SplQueue
     */
    protected function dumpChild(array $pair, \SplQueue $queue)
    {
        if (isset($pair['subNodes'])) {
            $queue = $pair['subNodes']->dump($queue);
        }
        return $queue;
    }

    /**
     * dump the indexed key into the queue if it is set
     * @param array $pair
     * @param \SplQueue $queue
     * @return \SplQueue
     */
    protected function dumpKey(array $pair, \SplQueue $queue)
    {
        if (isset($pair['keys'])) {
            $queue->enqueue($pair['keys']);
        }
        return $queue;
    }

    /**
     * get the depth of the tree just asking one subnode because the B-Tree has all its leaves in the same level
     * @return int
     */
    public function getDepth()
    {
        $depth = 1;
        if ($this->hasChildren()) {
            $randomSubNode = null;
            do {
                $randomSubNode = $this->subNodes[array_rand($this->subNodes)];
            } while (!$randomSubNode);
            $depth += $this->getChildDepth($randomSubNode);
        }
        return $depth;
    }

    public function hasChildren()
    {
        return count($this->subNodes) > 0;
    }

    /**
     * prune the node (its keys and children too)
     */
    public function prune()
    {
        foreach ($this->subNodes as $subNode) {
            $subNode->prune();
        }
        $this->subNodes = array();
        $this->value = array();
    }

    public function search($item)
    {
        throw new Exception('Unimplemented method!');
//        $result = null;
//        foreach ($this->items as $storedItem) {
//            if ($storedItem->key == $item) {
//                $result = $this;
//                break;
//            }
//        }
//        return $result;
    }

    /**
     * split the node into two subnodes and send the center key to the resultant parent
     */
    public function split()
    {
        $chunks = array_chunk($this->value, $this->minRange, true);
        $centerKey = array_shift($chunks[1]);
        if (!count($chunks[1]) && isset($chunks[2])) {
            $chunks[1] = $chunks[2];
            unset($chunks[2]);
        }

        $lowerChild = new BNode($chunks[0]);
        $lowerChild->setParent($this);
        $upperChild = new BNode($chunks[1]);
        $upperChild->setParent($this);

        $this->transferChildren($centerKey, $lowerChild, $upperChild);

        $lowerRange = $this->getRangeString(null, $centerKey);
        $upperRange = $this->getRangeString($centerKey, null);

        $this->value = array($centerKey);
        $this->subNodes = array($lowerRange => $lowerChild, $upperRange => $upperChild);

        if ($this->parent) {
            $this->parent->merge($this);
            $this->subNodes = $this->value = null;
        }
    }

    protected function getRangeString($from, $to)
    {
        $range = new Range($from, $to);
        return $range->__toString();
    }

    protected function transferChildren($centerKey, &$lowerChild, &$upperChild)
    {
        $prevKey = null;
        foreach ($this->value as $keyToMove) {
            $this->transferChild($centerKey, $lowerChild, $upperChild, $prevKey, $keyToMove);
            $prevKey = $keyToMove;
        }
        $range = $this->getRangeString($prevKey, null);
        if (isset($this->subNodes[$range])) {
            $this->attachNodeTo($upperChild, $this->subNodes[$range], $range);
        }
    }

    protected function transferChild($centerKey, &$lowerChild, &$upperChild, $prevKey, $keyToMove)
    {
        $comparationResult = $keyToMove->compareWith($centerKey);
        $range = $this->getRangeString($prevKey, $keyToMove);
        if (isset($this->subNodes[$range])) {
            $subnode = $this->subNodes[$range];
            if ($comparationResult < 1) {
                $newKey = $comparationResult ? $range : $this->getRangeString($prevKey, null);
                $this->attachNodeTo($lowerChild, $subnode, $newKey);
            } else {
                $newKey = ($prevKey->compareWith($centerKey)) ? $range : $this->getRangeString(null, $keyToMove);
                $this->attachNodeTo($upperChild, $subnode, $newKey);
            }
        }
    }

    protected function attachNodeTo(&$rootNode, $subNode, $key)
    {
        $subNode->setParent($rootNode);
        $rootNode->setSubNode($key, $subNode);
    }

    public function setSubNode($key, $indexToSet)
    {
        $this->subNodes[$key] = $indexToSet;
    }

    /**
     * merge the node with the former subnode (now it has just one key and two subnodes, because it is splitted)
     * @param BNode $formerSubNode
     */
    public function merge($formerSubNode)
    {
        $this->removeSubNode($formerSubNode);
        $keys = array_keys($this->value);
        $formerLowerIndexedKey = $this->value[$keys[0]];
        $formerUpperIndexedKey = $this->value[$keys[count($keys) - 1]];
        $keyToAdd = array_pop($formerSubNode->value);
        $subNodes = $formerSubNode->getSubNodes();
        $keyToFix = $fixedKey = null;
        if ($keyToAdd->compareWith($formerLowerIndexedKey) == -1) {
            $fixedKey = $this->getRangeString($keyToAdd, $formerLowerIndexedKey);
            $keyToFix = $this->getRangeString($keyToAdd, null);
        } else if ($keyToAdd->compareWith($formerUpperIndexedKey) == 1) {
            $fixedKey = $this->getRangeString($formerUpperIndexedKey, $keyToAdd);
            $keyToFix = $this->getRangeString(null, $keyToAdd);
        }

        $this->insertItems($keyToAdd);
        foreach ($subNodes as $key => $subtree) {
            $subtree->setParent($this);
            if ($key === $keyToFix) {
                $key = $fixedKey;
            }
            $this->subNodes[$key] = $subtree;
        }
        $this->fixRangeKeys();
        $this->check();
    }

    public function getSubNodeWhereInsert($item)
    {
        $multiIterator = $this->getMultipleIterator();

        $subNode = null;
        foreach ($multiIterator as $pair) {
            if (isset($pair['keys'])) {
                if ($pair['keys']->compareWith($item) == 1) {
                    $subNode = $pair['subNodes'];
                    break;
                }
            } else {
                $subNode = $pair['subNodes'];
                break;
            }
        }
        return $subNode;
    }

    /**
     * remove a subnode
     * @param BNode $node
     */
    public function removeSubNode($node)
    {
        foreach ($this->subNodes as $key => $subNode) {
            if ($node === $subNode) {
                $this->subNodes[$key] = null;
                unset($this->subNodes[$key]);
                break;
            }
        }
    }

    /**
     * get all the subnodes
     * @return array
     */
    public function getSubNodes()
    {
        return $this->subNodes;
    }

    /**
     * set the parent of the node
     * @param BNode $root
     */
    public function setParent($root)
    {
        $this->parent = $root;
    }

    /**
     * insert the received Index(es) into the key set
     * @param \Kpacha\Datastructure\Index|array $item
     */
    public function insertItems($item)
    {
        if (!is_array($item)) {
            $item = array($item);
        }
        foreach ($item as $newItem) {
            $this->value[] = $newItem;
        }
        usort($this->value, array('\Kpacha\Datastructure\Index', 'compare'));
    }

    public function check()
    {
        if (count($this->value) > $this->maxRange) {
            $this->split();
        }
    }

    /**
     * sort the subnodes
     */
    protected function fixRangeKeys()
    {
        ksort($this->subNodes);
    }

}
