<?php
namespace Kpacha\Datastructure;

/**
 * Simple Entity to be indexed
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
    
}
