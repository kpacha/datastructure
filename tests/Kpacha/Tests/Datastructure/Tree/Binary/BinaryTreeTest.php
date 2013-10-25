<?php

namespace Kpacha\Tests\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\Binary\BinaryTree;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * BinaryTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinaryTreeTest extends TestCase
{

    protected $_subject;

    public function setUp()
    {
        $this->_subject = new BinaryTree;
    }

    public function testIsEmpty()
    {
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testIsNotEmpty()
    {
        $this->populate();
        $this->assertFalse($this->_subject->isEmpty());
    }

    public function testPrune()
    {
        $this->populate();
        $this->_subject->prune();
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testDump()
    {
        $this->_subject->insert(7);
        $this->_subject->insert(3);
        $this->_subject->insert(9);
        $this->assertEquals('3, 7, 9, ', $this->_subject->dump());
    }

    public function testDumpEmpty()
    {
        $this->assertEquals('', $this->_subject->dump());
    }

    public function testSearchAndFoundAtRoot()
    {
        $this->populate();
        $this->assertEquals(10, $this->_subject->search(10)->value);
    }

    public function testSearchAndFound()
    {
        $this->populate();
        $this->assertEquals(60, $this->_subject->search(60)->value);
    }

    public function testSearchAndNotFound()
    {
        $this->populate();
        $this->assertNull($this->_subject->search(8));
    }
    
    private function populate()
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
