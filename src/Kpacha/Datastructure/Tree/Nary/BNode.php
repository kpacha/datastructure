<?php

namespace Kpacha\Datastructure\Tree\Nary;

use Kpacha\Datastructure\Tree\AbstractNode;

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
        foreach ($this->value as $key => $item) {
            if (isset($this->subNodes[$key])) {
                $queue = $this->subNodes[$key]->dump($queue);
            }
            $queue->enqueue($item);
        }
        if (isset($this->subNodes[count($this->value) - 1])) {
            $queue = $this->subNodes[count($this->value) - 1]->dump($queue);
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
        $centerKey = $this->value[$this->minRange];
        unset($this->value[$this->minRange]);
        $chunks = array_chunk($this->value, $this->minRange, true);

        $lowerChild = new BNode($chunks[0]);
        $upperChild = new BNode($chunks[1]);

        $this->value = array($centerKey->key => $centerKey);
        $this->subNodes = array($lowerChild, $upperChild);
    }

}
