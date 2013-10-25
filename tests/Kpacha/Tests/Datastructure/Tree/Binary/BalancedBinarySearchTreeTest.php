<?php

namespace Kpacha\Tests\Datastructure\Tree\Binary;

use Kpacha\Datastructure\Tree\Binary\BalancedBinarySearchTree;
use Kpacha\Datastructure\Index;
use \PHPUnit_Framework_TestCase as TestCase;

/**
 * BinaryTreeTest
 *
 * @author Kpacha <kpacha666@gmail.com>
 */
class BalancedBinarySearchTreeTest extends TestCase
{

    protected $_subject;
    protected $dummyIndexes = array();

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
        $this->_subject->insertBalanced(array(
            $this->buildIndex(-3), $this->buildIndex(0), $this->buildIndex(8), $this->buildIndex(19),
            $this->buildIndex(20), $this->buildIndex(31), $this->buildIndex(60), $this->buildIndex(61)
        ));
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
        $this->dummyIndexes = array(
            3 => $this->buildIndex(3),
            4 => $this->buildIndex(4),
            6 => $this->buildIndex(6),
            10 => $this->buildIndex(10),
            16 => $this->buildIndex(16),
            26 => $this->buildIndex(26),
            60 => $this->buildIndex(60),
            90 => $this->buildIndex(90)
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
