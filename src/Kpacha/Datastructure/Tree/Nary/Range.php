<?php

namespace Kpacha\Datastructure\Tree\Nary;

/**
 * Simple Range for subnode indexation
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class Range
{

    var $from = null;
    var $to = null;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function __toString()
    {
        $from = $to = null;
        if ($this->from) {
            $from = is_int($this->from->key) ? sprintf('%1$09d', $this->from->key) : $this->from->key;
        }
        if ($this->to) {
            $to = is_int($this->to->key) ? sprintf('%1$09d', $this->to->key) : $this->to->key;
        }
        return "$from -> $to";
    }

    static public function compare($range1, $range2)
    {
        if ($range1->isValid() && $range2->isValid()) {
            if ($range1->from->compareWith($range2->from) === 0 && $range1->to->compareWith($range2->to) === 0) {
                return 0;
            }
            if ($range1->to->compareWith($range2->from) == -1) {
                return -1;
            }
            if ($range2->to->compareWith($range1->from) == -1) {
                return 1;
            }
        }
        throw new \Exception("Invalid range!");
    }

    public function compareWith($index)
    {
        return self::compare($this, $index);
    }

    public function isValid()
    {
        $isValid = false;
        if ($this->from !== null && $this->to !== null) {
            $isValid = $this->from < $this->to;
        } else {
            $isValid = isset($this->from) || isset($this->to);
        }
        return $isValid;
    }

}
