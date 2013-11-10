<?php

namespace Kpacha\Datastructure\Tree\Nary;

/**
 * Simple Range for subnode indexation
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class Range
{

    const CONCATENATOR = ' -> ';

    public $from = null;
    public $to = null;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function __toString()
    {
        $from = $to = null;
        if ($this->from) {
            $from = $this->parseBoundary($this->from->key);
        }
        if ($this->to) {
            $to = $this->parseBoundary($this->to->key);
        }
        return $from . self::CONCATENATOR . $to;
    }

    private function parseBoundary($boundary)
    {
        return is_int($boundary) ? sprintf('%1$09d', $boundary) : $boundary;
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

    public function isInRange($value)
    {
        $value = $this->parseBoundary($value);
        return ($this->from === null || strcmp($this->from, $value) < 0) &&
                ($this->to === null || strcmp($this->to, $value) > 0);
    }

    public static function getRange($stringRange)
    {
        $boundaries = explode(self::CONCATENATOR, $stringRange);
        return new self($boundaries[0], $boundaries[1]);
    }

}
