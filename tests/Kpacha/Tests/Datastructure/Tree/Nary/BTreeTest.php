<?php

namespace Kpacha\Tests\Datastructure\Tree\Nary;

use Kpacha\Datastructure\Tree\Nary\BTree;
use Kpacha\Datastructure\Index;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * Description of BTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BTreeTest extends TestCase
{

    protected $_subject;

    public function setUp()
    {
        $this->_subject = new BTree;
    }

    public function testFirstInsert()
    {
        $this->_subject->insert($this->buildIndex(1));
        $this->assertFalse($this->_subject->isEmpty());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testSecondInsert()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2));
        $this->_subject->insert($indexes[0]);
        $this->_subject->insert($indexes[1]);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testSecondInsertSorted()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2));
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[0]);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testSplitDetection()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2), $this->buildIndex(3));
        $this->_subject->insert($indexes[0]);
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[2]);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testFourthInsertion()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2), $this->buildIndex(3), $this->buildIndex(4));
        $this->_subject->insert($indexes[0]);
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[3]);
        $this->_subject->insert($indexes[2]);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testInsertIntoFirstChildNode()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2), $this->buildIndex(3), $this->buildIndex(4));
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[3]);
        $this->_subject->insert($indexes[2]);
        $this->_subject->insert($indexes[0]);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testPrune()
    {
        $this->testInsertIntoFirstChildNode();
        $this->_subject->prune();
        $this->assertTrue($this->_subject->isEmpty());
        $this->assertEquals(0, $this->_subject->getDepth());
        $this->assertEquals(array(), $this->_subject->dump());
    }

    private function buildIndex($key, $value = 'some dummy data')
    {
        $entity = new Index;
        $entity->key = $key;
        $entity->value = $value;
        return $entity;
    }

}
