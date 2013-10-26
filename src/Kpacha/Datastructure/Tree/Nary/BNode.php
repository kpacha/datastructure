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

    protected function dumpChild(array $pair, \SplQueue $queue)
    {
        if (isset($pair['subNodes'])) {
            $queue = $pair['subNodes']->dump($queue);
        }
        return $queue;
    }

    protected function dumpKey(array $pair, \SplQueue $queue)
    {
        if (isset($pair['keys'])) {
            $queue->enqueue($pair['keys']);
        }
        return $queue;
    }

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
