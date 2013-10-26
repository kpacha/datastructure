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
        $multiIterator = new MultipleIterator(MultipleIterator::MIT_NEED_ANY | MultipleIterator::MIT_KEYS_ASSOC);
        $multiIterator->attachIterator(new ArrayIterator($this->value), 'keys');
        $multiIterator->attachIterator(new ArrayIterator($this->subNodes), 'subNodes');

        foreach ($multiIterator as $pair) {
            $queue = $this->dumpChild($pair, $queue);
            $queue = $this->dumpKey($pair, $queue);
        }
        return $queue;
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
            $depth += $this->getChildDepth($this->subNodes[0]);
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
        $upperChild = new BNode($chunks[1]);

        $this->value = array($centerKey->key => $centerKey);
        $this->subNodes = array($lowerChild, $upperChild);
    }

}
