<?php

namespace Kpacha\Tests\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\Binary\BinarySearchTree;
use Kpacha\Datastructure\Index;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * BinarySearchTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BinarySearchTreeTest extends TestCase
{

    protected $_subject;
    protected $dummyIndexes = array();

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
        $this->assertEquals(array(
            $this->dummyIndexes[3], $this->dummyIndexes[4], $this->dummyIndexes[6], $this->dummyIndexes[10],
            $this->dummyIndexes[16], $this->dummyIndexes[26], $this->dummyIndexes[60], $this->dummyIndexes[90]
                ), $this->_subject->dump());
    }

    public function testDumpEmpty()
    {
        $this->assertEquals(array(), $this->_subject->dump());
    }

    public function testSearchAndFoundAtRoot()
    {
        $this->populateBalanced();
        $this->assertEquals($this->dummyIndexes[10], $this->_subject->search(10)->value);
    }

    public function testSearchAndFound()
    {
        $this->populateBalanced();
        $this->assertEquals($this->dummyIndexes[60], $this->_subject->search(60)->value);
    }

    public function testSearchAndNotFound()
    {
        $this->populateBalanced();
        $this->assertNull($this->_subject->search(8));
    }

    public function testRemoveUniqueNode()
    {
        $this->_subject->insert($this->buildIndex(3));
        $this->_subject->remove(3);
        $this->assertTrue($this->_subject->isEmpty());
    }

    public function testRemoveRootNodeWithOneLeafRight()
    {
        $entity = $this->buildIndex(4);
        $this->_subject->insert($this->buildIndex(3));
        $this->_subject->insert($entity);
        $this->_subject->remove(3);
        $this->assertEquals(array($entity), $this->_subject->dump());
    }

    public function testRemoveRootNodeWithOneLeafLeft()
    {
        $entity = $this->buildIndex(1);
        $this->_subject->insert($this->buildIndex(3));
        $this->_subject->insert($entity);
        $this->_subject->remove(3);
        $this->assertEquals(array($entity), $this->_subject->dump());
    }

    public function testRemoveRootNodeWithLeaves()
    {
        $entities = array($this->buildIndex(1), $this->buildIndex(5));
        $this->_subject->insert($this->buildIndex(3));
        $this->_subject->insert($entities[0]);
        $this->_subject->insert($entities[1]);
        $this->_subject->remove(3);
        $this->assertEquals($entities, $this->_subject->dump());
    }

    public function testRemoveNotRootNode()
    {
        $entities = array($this->buildIndex(1), $this->buildIndex(4), $this->buildIndex(5));
        $this->_subject->insert($entities[0]);
        $this->_subject->insert($entities[1]);
        $this->_subject->insert($this->buildIndex(3));
        $this->_subject->insert($entities[2]);
        $this->_subject->remove(3);
        $this->assertEquals($entities, $this->_subject->dump());
    }

    public function testRemoveNode()
    {
        $this->populateBalanced();
        $this->_subject->remove(16);
        $this->_subject->remove(6);
        $this->assertEquals(array(
            $this->dummyIndexes[3], $this->dummyIndexes[4], $this->dummyIndexes[10],
            $this->dummyIndexes[26], $this->dummyIndexes[60], $this->dummyIndexes[90]
                ), $this->_subject->dump());
    }

    public function testGetDepth()
    {
        $this->populateBalanced();
        $this->assertEquals(3, $this->_subject->getDepth());
    }

    private function populateBalanced()
    {
        $this->dummyIndexes = array(
            10 => $this->buildIndex(10),
            3 => $this->buildIndex(3),
            6 => $this->buildIndex(6),
            90 => $this->buildIndex(90),
            4 => $this->buildIndex(4),
            26 => $this->buildIndex(26),
            16 => $this->buildIndex(16),
            60 => $this->buildIndex(60)
        );

        foreach ($this->dummyIndexes as $entity) {
            $this->_subject->insert($entity);
        }
    }

    private function buildIndex($key, $value = 'some dummy data')
    {
        $entity = new Index;
        $entity->key = $key;
        $entity->value = $value;
        return $entity;
    }

}
