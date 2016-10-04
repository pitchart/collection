<?php

namespace Pitchart\Collection\Test;


use Pitchart\Collection\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{

    public function testCanBeInstantiated() {
        $collection = new Collection(array());
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(\Countable::class, $collection);
        $this->assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testCanBeBuilded() {
        $collection = Collection::from(array());
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(\Countable::class, $collection);
        $this->assertInstanceOf(\ArrayAccess::class, $collection);
    }

    /**
     * @param array $items
     * @param int $numberOfItems
     * @dataProvider countTestProvider
     */
    public function testCanReturnNumberOfItems(array $items, $numberOfItems) {
        $collection = new Collection($items);
        $this->assertEquals($numberOfItems, $collection->count());
    }

    /**
     * @param array $items
     * @param callback $reducer
     * @param mixed $initial
     * @param mixed $expected
     * @dataProvider reduceTestProvider
     */
    public function testCanBeReduced($items, $reducer, $initial, $expected) {
        $collection = new Collection($items);
        $this->assertEquals($expected, $collection->reduce($reducer, $initial));
    }

    public function countTestProvider() {
        return [
            'An empty array' => [[], 0],
            'An array with elements' => [[1,2,3], 3],
        ];
    }

    public function reduceTestProvider() {
        return [
            'Sum reducing' => [[1,2,3,4], function($accumulator, $item) {return $accumulator + $item;}, 0, 10],
            'String reducing' => [['banana', 'apple', 'orange'], function($accumulator, $item) {return trim($accumulator.', '.$item, ', ');}, '', 'banana, apple, orange'],
        ];
    }

}
