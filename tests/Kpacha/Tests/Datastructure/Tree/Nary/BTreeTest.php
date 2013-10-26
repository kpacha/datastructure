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
    }

    public function testSecondInsert()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2));
        $this->_subject->insert($indexes[0]);
        $this->_subject->insert($indexes[1]);
        $this->assertEquals($indexes, $this->_subject->dump());
    }

    public function testSecondInsertSorted()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2));
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[0]);
        $this->assertEquals($indexes, $this->_subject->dump());
    }

    public function testSplitDetection()
    {
        $indexes = array($this->buildIndex(1), $this->buildIndex(2), $this->buildIndex(3));
        $this->_subject->insert($indexes[0]);
        $this->_subject->insert($indexes[1]);
        $this->_subject->insert($indexes[2]);
        $this->assertEquals($indexes, $this->_subject->dump());
    }

    private function buildIndex($key, $value = 'some dummy data')
    {
        $entity = new Index;
        $entity->key = $key;
        $entity->value = $value;
        return $entity;
    }

}
