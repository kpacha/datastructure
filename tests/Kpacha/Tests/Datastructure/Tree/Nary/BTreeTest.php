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
        $this->doTest(array(), true, 0);
    }

    public function testFirstInsert()
    {
        $indexes = $this->insertSequence(1);
        $this->assertEquals($indexes, $this->_subject->dump());
        $this->doTest($indexes, false, 0);
    }

    public function testSecondInsertUnsorted()
    {
        $indexes = $this->insertSequence(2, 1);
        $this->doTest($indexes, false, 0);
    }

    public function testSplitDetection()
    {
        $indexes = $this->insertSequence(1, 2, 3);
        $this->doTest($indexes, false, 1);
    }

    public function testFourthInsertion()
    {
        $indexes = $this->insertSequence(1, 2, 4, 3);
        $this->doTest($indexes, false, 1);
    }

    public function testInsertIntoFirstChildNode()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1);
        $this->doTest($indexes, false, 1);
    }

    public function testFirstLeftChildNodeSplit()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1, 0);
        $this->doTest($indexes, false, 1);
    }

    public function testFirstRightChildNodeSplit()
    {
        $indexes = $this->insertSequence(0, 1, 2, 3, 4);
        $this->doTest($indexes, false, 1);
    }

    public function testDepthOf2()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1, 0, 10, 16);
        $this->doTest($indexes, false, 2);
    }

    public function testPrune()
    {
        $this->insertSequence(2, 4, 3, 1, 0);
        $this->_subject->prune();
        $this->testEmpty();
    }

    public function testDepthOf3()
    {
        $indexes = $this->insertSequence(2, 4, 3, 1, 0, 10, 16, 19, 30, 36, 40, 37, 50, 55, 60);
        $this->doTest($indexes, false, 3);
    }

    public function testDepthOf4()
    {
        $indexes = $this->insertSequence(0, 1, 2, 3, 4, 10, 16, 19, 30, 36, 37, 40, 50, 55, 60, 61, 62, 65, 66, 67, 68,
                75, 76, 80, 81, 82, 83, 84, 85, 86, 87);
        $this->doTest($indexes, false, 4);
    }

    public function testSearchAndFoundAtRoot()
    {
        $key=10;
        $newIndex = $this->buildIndex($key);
        $this->_subject->insert($newIndex);
        $this->assertEquals($newIndex, $this->_subject->search($key));
    }

    public function testSearchAndNotFound()
    {
        $this->assertNull($this->_subject->search(8));
    }

    private function doTest($expectedDump, $isEmpty, $expectedDepth)
    {
        $this->assertEquals($expectedDump, $this->_subject->dump());
        $this->assertEquals($isEmpty, $this->_subject->isEmpty());
        $this->assertEquals($expectedDepth, $this->_subject->getDepth());
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
