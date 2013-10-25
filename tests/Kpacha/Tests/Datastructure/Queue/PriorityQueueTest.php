<?php

namespace Kpacha\Tests\Datastructure\Queue;

use Kpacha\Datastructure\Queue\PriorityQueue;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Simple PriorityQueue Test
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class PriorityQueueTest extends TestCase
{

    protected $_subject;

    public function setUp()
    {
        $this->_subject = new PriorityQueue;
    }

    public function testSimple()
    {
        $this->_subject->insert('G', 1);
        $this->_subject->insert('A', 4);
        $this->_subject->insert('B', 3);
        $this->_subject->insert('C', 5);
        $this->_subject->insert('D', 8);
        $this->_subject->insert('E', 2);
        $this->_subject->insert('F', 7);
        $this->_subject->insert('G', 1);
        $this->_subject->insert('H', 6);
        $this->_subject->insert('I', 0);

        $messages = '';
        while ($this->_subject->valid()) {
            $messages .= $this->_subject->current();
            $this->_subject->next();
        }

        $this->assertEquals('IGGEBACHFD', $messages);
    }

}
