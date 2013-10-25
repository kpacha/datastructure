<?php

namespace Kpacha\Tests\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\Binary\BalancedBinarySearchTree;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * BinaryTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BalancedBinarySearchTreeTest extends TestCase
{

    public function setUp()
    {
        $this->_subject = new BalancedBinarySearchTree;
    }

    public function testBalance()
    {
        $this->populateWorstCase();
        $this->assertEquals(7, $this->_subject->getDepth());
        $this->_subject->balance();
        $this->assertEquals(3, $this->_subject->getDepth());
    }

    public function testInsertBalanced()
    {
        $this->_subject->insertBalanced(array(-3, 0, 8, 19, 20, 31, 60, 61));
        $this->assertEquals(3, $this->_subject->getDepth());
    }

    public function testBalanceIsIdempotent()
    {
        $this->testInsertBalanced();
        $this->_subject->balance();
        $this->assertEquals(3, $this->_subject->getDepth());
    }

    private function populateWorstCase()
    {
        $this->_subject->insert(3);
        $this->_subject->insert(4);
        $this->_subject->insert(6);
        $this->_subject->insert(10);
        $this->_subject->insert(16);
        $this->_subject->insert(26);
        $this->_subject->insert(60);
        $this->_subject->insert(90);
    }

}
