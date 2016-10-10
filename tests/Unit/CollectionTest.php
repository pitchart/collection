<?php

namespace Pitchart\Collection\Test\Unit;


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

    public function testCanIterateItems() {
        $collection = Collection::from([1, 2, 3, 4]);
        $mock = $this->getMockBuilder(stdClass::class)
                     ->setMethods(['test'])
                     ->getMock();

        $mock->expects($this->exactly(4))
            ->method('test')
        ;
        $function = function($item) use ($mock) {
            $mock->test($item);
        };

        $collection->each($function);
    }

    /**
     * @param array $items
     * @param callable $reducer
     * @param mixed $initial
     * @param mixed $expected
     * @dataProvider reduceTestProvider
     */
    public function testCanBeReduced(array $items, callable $reducer, $initial, $expected) {
        $collection = new Collection($items);
        $this->assertEquals($expected, $collection->reduce($reducer, $initial));
    }

    /**
     * @param array $items
     * @param callable $callback
     * @param array $expected
     * @dataProvider filterTestProvider
     */
    public function testCanBeFiltered(array $items, callable $callback, array $expected) {
        $collection = new Collection($items);
        $this->assertEquals($expected, array_values($collection->filter($callback)->toArray()));
        // test the alias
        $this->assertEquals($expected, array_values($collection->select($callback)->toArray()));
    }

    /**
     * @param array $items
     * @param callable $callback
     * @param array $expected
     * @dataProvider rejectTestProvider
     */
    public function testCanBeRejected(array $items, callable $callback, array $expected) {
        $collection = new Collection($items);
        $this->assertEquals($expected, array_values($collection->reject($callback)->toArray()));
    }

    /**
     * @param array $items
     * @param callable $callback
     * @param array $expected
     * @dataProvider mapTestProvider
     */
    public function testCanMapDatas(array $items, callable $callback, array $expected) {
        $this->assertEquals($expected, Collection::from($items)->map($callback)->toArray());
    }

    public function testCanGroupItems() {
        $testItem1 = (object) ['name' => 'bar', 'age' => 20];
        $testItem2 = (object) ['name' => 'fizz', 'age' => 30];
        $items = [
            (object) ['name' => 'foo', 'age' => 10],
            $testItem1,
            (object) ['name' => 'baz', 'age' => 25],
            $testItem2,
            (object) ['name' => 'buzz', 'age' => 40],
        ];
        $collection = Collection::from($items);
        $grouped = $collection->groupBy(function($item) {return $item->age <= 25;});

        // Grouped Collection only contains instances of Collection
        foreach ($grouped as $group) {
            $this->assertInstanceOf(Collection::class, $group);
        }
        // Group by a boolean function returns 2 groups
        $this->assertEquals(2, $grouped->count());
        // Test items distribution
        $this->assertContains($testItem1, $grouped->offsetGet(1));
        $this->assertContains($testItem2, $grouped->offsetGet(0));
    }

    public function testCanMergeCollections() {
        $collection = new Collection([1, 2, 3]);
        $merged = $collection->merge(Collection::from([4, 5, 6]));
        $this->assertEquals([1, 2, 3, 4, 5, 6], $merged->values());
    }

    public function testCanCollapseCollectionOfCollections() {
        $items = [
            Collection::from([1, 2, 3]),
            Collection::from([4, 5, 6]),
        ];

        $expected = [1, 2, 3, 4, 5, 6];

        $collection = Collection::from($items)->concat();

        $this->assertEquals($expected, $collection->values());
    }

    public function testCanRemoveDuplicates() {
        $items = [1, 6, 3, 4, 3, 5, 5, 3, 2, 1];
        $collection = Collection::from($items)->distinct();

        foreach ($collection->values() as $key => $value) {
            $datas = $collection->values();
            unset($datas[$key]);
            $this->assertNotContains($value, $datas);
        }
    }

    public function testCanSortItems() {
        $sorted = Collection::from([3, 1, 2, 4])->sort(function($first, $second) {return ($first == $second ? 0 : ($first < $second ? -1 : 1)); });
        $this->assertEquals([1, 2, 3, 4], $sorted->values());
    }

    public function testCanExtractParts() {
        $sliced = Collection::from([1, 2, 3, 4])->slice(1, 2);
        $this->assertEquals([2, 3], $sliced->values());
    }

    public function testCanExtratNthFirstItems() {
        $firsts = Collection::from([1, 2, 3, 4])->take(3);
        $this->assertEquals([1, 2, 3], $firsts->values());
    }

    public function testCanRemoveItemsFromAnotherCollection() {
        $difference = Collection::from([1, 2, 3, 4])->difference(new Collection([2, 3]));
        $this->assertEquals([1, 4], $difference->values());
    }

    public function testCanRetainItemsAlsoInAnotherCollection() {
        $intersection = Collection::from([1, 2, 3, 4])->intersection(new Collection([2, 3]));
        $this->assertEquals([2, 3], $intersection->values());
    }

    public function testCanFlattenElementsAfterAMapping() {
        $flatMap = Collection::from([1, 2, 3, 4])->flatMap(function($item) { return Collection::from([$item, $item + 1]);});
        $this->assertEquals([1, 2, 2, 3, 3, 4, 4, 5], $flatMap->values());
        $flatMap = Collection::from([1, 2, 3, 4])->mapcat(function($item) { return Collection::from([$item, $item + 1]);});
        $this->assertEquals([1, 2, 2, 3, 3, 4, 4, 5], $flatMap->values());
    }
    /**
     * Test that transformation methods keeps the collection immutable
     *
     * @param array $items
     * @param $func
     * @param callable $callback
     * @dataProvider immutabilityTestProvider
     */

    public function testMethodsKeepImmutability(array $items, $func, array $params) {
        $collection = Collection::from($items);
        call_user_func_array(array($collection, $func), $params);
        $this->assertEquals($items, $collection->toArray());
    }

    public function immutabilityTestProvider() {
        return [
            'each' => [[1, 2, 3, 4], 'each', [function($item) { return $item + 1; }]],
            'map' => [[1, 2, 3, 4], 'map', [function($item) { return $item + 1; }]],
            'filter' => [[1, 2, 3, 4], 'filter', [function($item) { return $item % 2 == 0;}]],
            'select' => [[1, 2, 3, 4], 'select', [function($item) { return $item % 2 == 0;}]],
            'reject' => [[1, 2, 3, 4], 'reject', [function($item) { return $item % 2 == 0;}]],
            'reduce' => [[1, 2, 3, 4], 'reduce', [function($accumulator, $item) { return $item + $accumulator;}, 0]],
            'sort' => [[1, 2, 3, 4], 'sort', [function($first, $second) {return ($first == $second ? 0 : ($first < $second ? -1 : 1)); }]],
            'slice' => [[1, 2, 3, 4], 'slice', [1, 2, false]],
            'slice preserving keys' => [[1, 2, 3, 4], 'slice', [1, 2, true]],
            'take' => [[1, 2, 3, 4], 'take', [3, false]],
            'take preserving keys' => [[1, 2, 3, 4], 'slice', [3, true]],
            'difference' => [[1, 2, 3, 4], 'difference', [new Collection([3, 4])]],
            'intersection' => [[1, 2, 3, 4], 'intersection', [new Collection([3, 4])]],
            'merge' => [[1, 2, 3, 4], 'merge', [new Collection([3, 4])]],
            'flatMap' => [[1, 2, 3, 4], 'flatMap', [function($item) { return Collection::from([$item, $item + 1]);}]],
            'mapcat' => [[1, 2, 3, 4], 'mapcat', [function($item) { return Collection::from([$item, $item + 1]);}]],
        ];
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

    public function mapTestProvider() {
        return [
            'Add 1 mapper' => [[1,2,3], function($item) {return $item + 1;}, [2, 3, 4]],
            'Concat mapper' => [['test1', 'test2', 'test3'], function($item) { return $item.'1';}, ['test11', 'test21', 'test31']],
            'Empty data mapper' => [[], function($item) { return $item + 1;}, []],
        ];
    }

    public function filterTestProvider() {
        return [
            'Pair filter' => [[1,2,3, 4], function($item) {return $item % 2 == 0;}, [2, 4]],
            'String filter' => [['foo', 'bar', 'fizz', 'buzz'], function($item) { return strpos($item, 'f') !== false;}, ['foo', 'fizz']],
        ];
    }

    public function rejectTestProvider() {
        return [
            'Pair filter' => [[1,2,3, 4], function($item) {return $item % 2 == 0;}, [1, 3]],
            'String filter' => [['foo', 'bar', 'fizz', 'buzz'], function($item) { return strpos($item, 'f') !== false;}, ['bar', 'buzz']],
        ];
    }

}
