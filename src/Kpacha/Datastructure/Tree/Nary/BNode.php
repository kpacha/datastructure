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
        $result = $this->serchInLocalStored($item);
        if (!$result) {
            $result = $this->searchInSubNodes($item);
        }
        return $result;
    }

    private function serchInLocalStored($item)
    {
        $result = null;
        foreach ($this->value as $storedItem) {
            if ($storedItem->key == $item) {
                $result = $storedItem;
                break;
            }
        }
        return $result;
    }

    private function searchInSubNodes($item)
    {
        $result = null;
        $ranges = array_keys($this->subNodes);
        $totalItems = count($this->value);
        for ($current = 0; $current <= $totalItems; $current++) {
            if(($result = $this->searchInSubNode($ranges, $current, $item))){
                break;
            }
        }
        return $result;
    }

    private function searchInSubNode($ranges, $current, $item)
    {
        $result = null;
        if (isset($ranges[$current]) && $this->getRange($ranges[$current])->isInRange($item)) {
            $result = $this->subNodes[$ranges[$current]]->search($item);
        }
        return $result;
    }

    private function getRange($stringRange)
    {
        return Range::getRange($stringRange);
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

        $lowerChild = $this->createChild($chunks[0]);
        $upperChild = $this->createChild($chunks[1]);

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

    private function createChild($data)
    {
        $child = new BNode($data);
        $child->setParent($this);
        return $child;
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
                $targetChild = $lowerChild;
            } else {
                $newKey = ($prevKey->compareWith($centerKey)) ? $range : $this->getRangeString(null, $keyToMove);
                $targetChild = $upperChild;
            }
            $this->attachNodeTo($targetChild, $subnode, $newKey);
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
        $keyToAdd = array_pop($formerSubNode->value);
        $subNodes = $formerSubNode->getSubNodes();
        $rangeKeyToFixData = $this->getRangeKeyToFixData($keyToAdd);

        $this->insertItems($keyToAdd);
        foreach ($subNodes as $key => $subtree) {
            $subtree->setParent($this);
            if ($key === $rangeKeyToFixData["keyToFix"]) {
                $key = $rangeKeyToFixData["fixedKey"];
            }
            $this->subNodes[$key] = $subtree;
        }
        $this->fixRangeKeys();
        $this->check();
    }

    private function getRangeKeyToFixData($keyToAdd)
    {
        $keys = array_keys($this->value);
        $formerLowerIndexedKey = $this->value[$keys[0]];
        $formerUpperIndexedKey = $this->value[$keys[count($keys) - 1]];
        $result = array();
        if ($keyToAdd->compareWith($formerLowerIndexedKey) == -1) {
            $result["fixedKey"] = $this->getRangeString($keyToAdd, $formerLowerIndexedKey);
            $result["keyToFix"] = $this->getRangeString($keyToAdd, null);
        } else if ($keyToAdd->compareWith($formerUpperIndexedKey) == 1) {
            $result["fixedKey"] = $this->getRangeString($formerUpperIndexedKey, $keyToAdd);
            $result["keyToFix"] = $this->getRangeString(null, $keyToAdd);
        }
        return $result;
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
