<?php

namespace Kpacha\Tests\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\Binary\BinarySearchTree;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * BinarySearchTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinarySearchTreeTest extends TestCase
{

    protected $_subject;

    public function setUp()
    {
        $this->_subject = new BinarySearchTree;
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testIsNotEmpty()
    {
        $this->populateBalanced();
        $this->assertFalse($this->_subject->isEmpty());
    }

    public function testPrune()
    {
        $this->populateBalanced();
        $this->_subject->prune();
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testDump()
    {
        $this->populateBalanced();
        $this->assertEquals(array(3, 4, 6, 10, 16, 26, 60, 90), $this->_subject->dump());
    }

    public function testDumpEmpty()
    {
        $this->assertEquals(array(), $this->_subject->dump());
    }

    public function testSearchAndFoundAtRoot()
    {
        $this->populateBalanced();
        $this->assertEquals(10, $this->_subject->search(10)->value);
    }

    public function testSearchAndFound()
    {
        $this->populateBalanced();
        $this->assertEquals(60, $this->_subject->search(60)->value);
    }

    public function testSearchAndNotFound()
    {
        $this->populateBalanced();
        $this->assertNull($this->_subject->search(8));
    }

    public function testRemoveUniqueNode()
    {
        $this->_subject->insert(3);
        $this->_subject->remove(3);
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testRemoveRootNodeWithOneLeafRight()
    {
        $this->_subject->insert(3);
        $this->_subject->insert(4);
        $this->_subject->remove(3);
        $this->assertEquals(array(4), $this->_subject->dump());
    }

    public function testRemoveRootNodeWithOneLeafLeft()
    {
        $this->_subject->insert(3);
        $this->_subject->insert(1);
        $this->_subject->remove(3);
        $this->assertEquals(array(1), $this->_subject->dump());
    }

    public function testRemoveRootNodeWithLeaves()
    {
        $this->_subject->insert(3);
        $this->_subject->insert(1);
        $this->_subject->insert(5);
        $this->_subject->remove(3);
        $this->assertEquals(array(1, 5), $this->_subject->dump());
    }

    public function testRemoveNotRootNode()
    {
        $this->_subject->insert(3);
        $this->_subject->insert(1);
        $this->_subject->insert(5);
        $this->_subject->insert(4);
        $this->_subject->remove(3);
        $this->assertEquals(array(1, 4, 5), $this->_subject->dump());
    }

    public function testRemoveNode()
    {
        $this->populateBalanced();
        $this->_subject->remove(16);
        $this->_subject->remove(6);
        $this->assertEquals(array(3, 4, 10, 26, 60, 90), $this->_subject->dump());
    }

    public function testGetDepth()
    {
        $this->populateBalanced();
        $this->assertEquals(3, $this->_subject->getDepth());
    }

    private function populateBalanced()
    {
        $this->_subject->insert(10);
        $this->_subject->insert(3);
        $this->_subject->insert(6);
        $this->_subject->insert(90);
        $this->_subject->insert(4);
        $this->_subject->insert(26);
        $this->_subject->insert(16);
        $this->_subject->insert(60);
    }

}
