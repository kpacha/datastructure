<?php

namespace Kpacha\Datastructure\Tree\Nary;

use Kpacha\Datastructure\Tree\AbstractNode;
use Kpacha\Datastructure\Index;
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

    public function __construct($items, &$parent = null, $minRange = self::MIN_RANGE)
    {
        parent::__construct((is_array($items) ? $items : array($items->key => $items)));
        $this->parent = $parent;
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
        $multiIterator->attachIterator(new ArrayIterator($this->value), 'keys');
        $multiIterator->attachIterator(new ArrayIterator($this->subNodes), 'subNodes');
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

        $lowerRange = new Range(null, $centerKey);
        $lowerChild = new BNode($chunks[0], $this);
        $upperRange = new Range($centerKey, null);
        $upperChild = new BNode($chunks[1], $this);

        $this->value = array($centerKey);
        $this->subNodes = array($lowerRange->__toString() => $lowerChild, $upperRange->__toString() => $upperChild);
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
     * Add a subnode
     * @param String $key
     * @param BNode $value
     */
    public function setSubNode($key, $value)
    {
        $this->subNodes[$key] = $value;
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
     * insert new Index into the key set
     * @param \Kpacha\Datastructure\Index $item
     */
    public function insertItems(Index $item)
    {
        if (!is_array($item)) {
            $item = array($item);
        }
        foreach ($item as $newItem) {
            $this->value[] = $newItem;
        }
        usort($this->value, array('\Kpacha\Datastructure\Index', 'compare'));
    }

    /**
     * sort the subnodes
     */
    public function fixRangeKeys()
    {
        ksort($this->subNodes);
    }

}
