<?php

namespace Kpacha\Datastructure;

/**
 * Simple key-value pair to be indexed
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class Index
{

    var $key;
    var $value;

    public function __toString()
    {
        return "$this->key -> $this->value";
    }

    static public function compare($index1, $index2)
    {
        if ($index1->key == $index2->key) {
            return 0;
        }
        return ($index1->key < $index2->key) ? -1 : 1;
    }

    public function compareWith($index)
    {
        return self::compare($this, $index);
    }

}
