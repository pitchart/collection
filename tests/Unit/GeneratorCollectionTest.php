<?php

namespace Pitchart\Collection\Test\Unit;

use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

class GeneratorCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInstantiated()
    {
        $collection = new GeneratorCollection(new \ArrayIterator(array()));
        self::assertInstanceOf(GeneratorCollection::class, $collection);
    }

    public function testCanBeBuilded()
    {
        $fromArray = GeneratorCollection::from([]);
        self::assertInstanceOf(GeneratorCollection::class, $fromArray);

        $fromIterator = GeneratorCollection::from(new \ArrayIterator([]));
        self::assertInstanceOf(GeneratorCollection::class, $fromIterator);

        $fromAggregate = GeneratorCollection::from(new \ArrayObject([]));
        self::assertInstanceOf(GeneratorCollection::class, $fromAggregate);
    }

    /**
     * @param mixed $argument
     * @param string $type
     * @dataProvider badArgumentProvider
     */
    public function testBuildFromBadArgumentThrowsAnException($argument, $type) {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf('Argument 1 must be an instance of Traversable or an array, %s given', $type));
        $collection = GeneratorCollection::from($argument);
    }

    /**
     * @param array    $items
     * @param callable $callback
     * @param array    $expected
     * @dataProvider mapTestProvider
     */
    public function testCanMapDatas(array $items, callable $callback, array $expected)
    {
        self::assertEquals($expected, GeneratorCollection::from($items)->map($callback)->toArray());
    }

    /**
     * @param array    $items
     * @param callable $callback
     * @param array    $expected
     * @dataProvider filterTestProvider
     */
    public function testCanBeFiltered(array $items, callable $callback, array $expected)
    {
        $collection = new GeneratorCollection(new \ArrayIterator($items));
        self::assertEquals($expected, $collection->filter($callback)->toArray());
        // test the alias
        self::assertEquals($expected, $collection->select($callback)->toArray());
    }

    public function testCanGetValuesAfterMappingOrFiltering() {
        $collection = new GeneratorCollection(new \ArrayIterator([0,1,2,3,4,5,6]));
        $values = $collection->map(function($item) { return $item; })->toArray();
        self::assertEquals([0,1,2,3,4,5,6], $values);

        $collection = new GeneratorCollection(new \ArrayIterator([0,1,2,3,4,5,6]));
        $values = $collection->filter(function($item) { return $item % 2 == 0; })->toArray();
        self::assertEquals([0,2,4,6], $values);
    }

    public function testCanTransformIntoCollection() {
        $persisted = GeneratorCollection::from([0,1,2,3,4,5,6])->persist();
        self::assertInstanceOf(Collection::class, $persisted);
        self::assertEquals([0,1,2,3,4,5,6], $persisted->values());
    }

    /**
     * @param array    $items
     * @param callable $callback
     * @param array    $expected
     * @dataProvider rejectTestProvider
     */
    public function testCanBeRejected(array $items, callable $callback, array $expected)
    {
        self::assertEquals($expected, GeneratorCollection::from($items)->reject($callback)->toArray());
    }

    public function testCanMergeCollections()
    {
        self::assertEquals([1, 2, 3, 4, 5, 6], GeneratorCollection::from([1, 2, 3])->merge(GeneratorCollection::from([4, 5, 6]))->toArray());
    }

    /**
     * @param array    $items
     * @param callable $reducer
     * @param mixed    $initial
     * @param mixed    $expected
     * @dataProvider reduceTestProvider
     */
    public function testCanBeReduced(array $items, callable $reducer, $initial, $expected)
    {
        self::assertEquals($expected, GeneratorCollection::from($items)->reduce($reducer, $initial));
    }

    public function badArgumentProvider() {
        return [
            [null, 'NULL'],
            [true, 'boolean'],
            ['toto', 'string'],
            [15, 'integer'],
            [new \stdClass(), 'stdClass'],
        ];
    }

    public function mapTestProvider()
    {
        return [
            'Empty data set' => [[], function ($item) {
                return $item + 1;
            }, []],
            'Add 1 mapper' => [[1,2,3], function ($item) {
                return $item + 1;
            }, [2, 3, 4]],
            'Concat mapper' => [['test1', 'test2', 'test3'], function ($item) {
                return $item.'1';
            }, ['test11', 'test21', 'test31']],
            'Empty data mapper' => [[], function ($item) {
                return $item + 1;
            }, []],
        ];
    }

    public function filterTestProvider()
    {
        return [
            'Empty data set' => [[], function ($item) {
                return $item % 2 == 0;
            }, []],
            'Pair filter' => [[1,2,3, 4], function ($item) {
                return $item % 2 == 0;
            }, [2, 4]],
            'String filter' => [['foo', 'bar', 'fizz', 'buzz'], function ($item) {
                return strpos($item, 'f') !== false;
            }, ['foo', 'fizz']],
        ];
    }

    public function reduceTestProvider()
    {
        return [
            'Empty data set' => [[], function ($accumulator, $item) {
                return $accumulator + $item;
            }, 0, 0],
            'Sum reducing' => [[1,2,3,4], function ($accumulator, $item) {
                return $accumulator + $item;
            }, 0, 10],
            'String reducing' => [['banana', 'apple', 'orange'], function ($accumulator, $item) {
                return trim($accumulator.', '.$item, ', ');
            }, '', 'banana, apple, orange'],
        ];
    }

    public function rejectTestProvider()
    {
        return [
            'Empty data set' => [[], function ($item) {
                return $item % 2 == 0;
            }, []],
            'Pair filter' => [[1,2,3, 4], function ($item) {
                return $item % 2 == 0;
            }, [1, 3]],
            'String filter' => [['foo', 'bar', 'fizz', 'buzz'], function ($item) {
                return strpos($item, 'f') !== false;
            }, ['bar', 'buzz']],
        ];
    }

}
