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

    public function testEmpty()
    {
        $this->assertEquals(array(), $this->_subject->dump());
        $this->assertTrue($this->_subject->isEmpty());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testFirstInsert()
    {
        $indexes = $this->insertSequence(1);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertFalse($this->_subject->isEmpty());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testSecondInsertUnsorted()
    {
        $indexes = $this->insertSequence(2, 1);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(0, $this->_subject->getDepth());
    }

    public function testSplitDetection()
    {
        $indexes = $this->insertSequence(1, 2, 3);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testFourthInsertion()
    {
        $indexes = $this->insertSequence(1, 2, 4, 3);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testInsertIntoFirstChildNode()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testFirstChildNodeSplit()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1, 0);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->assertEquals(1, $this->_subject->getDepth());
    }

    public function testPrune()
    {
        $this->insertSequence(2, 4, 3, 1, 0);
        $this->_subject->prune();
        $this->assertTrue($this->_subject->isEmpty());
        $this->assertEquals(0, $this->_subject->getDepth());
        $this->assertEquals(array(), $this->_subject->dump());
    }

    private function insertSequence()
    {
        $sequence = func_get_args();
        $indexes = array();
        foreach ($sequence as $newKey) {
            $newIndex = $this->buildIndex($newKey);
            $indexes[$newKey] = $newIndex;
            $this->_subject->insert($newIndex);
        }
        ksort($indexes);
        return array_values($indexes);
    }

    private function buildIndex($key, $value = 'some dummy data')
    {
        $entity = new Index;
        $entity->key = $key;
        $entity->value = $value;
        return $entity;
    }

}
