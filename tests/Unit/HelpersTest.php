<?php

namespace Pitchart\Collection\Test\Unit;

use Pitchart\Collection\Helpers as ch;
use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

    public function testCollectReturnsACollection()
    {
        $collection = ch\collect([1, 2, 3, 4]);
        self::assertInstanceOf(Collection::class, $collection);
    }

    public function testGeneratorRetursAGeneratorCollection()
    {
        $collection = ch\generator([1, 2, 3, 4]);
        self::assertInstanceOf(GeneratorCollection::class, $collection);
    }

    public function testMapReturnsAMappedCollection()
    {
        $collection = ch\map([1,2,3,4], function ($item) {
            return $item + 1;
        });
        self::assertInstanceOf(Collection::class, $collection);
        self::assertEquals([2,3,4,5], $collection->values());
    }

    /**
     * @param  iterable $iterable
     * @dataProvider iterableProvider
     */
    public function testMapHelperCanMapAnyIterable($iterable)
    {
        self::assertEquals([2, 3, 4, 5], ch\map($iterable, function ($item) {
            return $item + 1;
        })->values());
    }

    public function testFilterReturnsAFilteredCollection()
    {
        $collection = ch\filter([1,2,3,4], function ($item) {
            return $item % 2 == 0;
        });
        self::assertInstanceOf(Collection::class, $collection);
        self::assertEquals([2,4], $collection->values());
    }

    /**
     * @param  iterable $iterable
     * @dataProvider iterableProvider
     */
    public function testFilterHelperCanFilterAnyIterable($iterable)
    {
        self::assertEquals([2, 4], ch\filter($iterable, function ($item) {
            return $item % 2 == 0;
        })->values());
    }

    public function testRejectReturnsAFilteredCollection()
    {
        $collection = ch\reject([1,2,3,4], function ($item) {
            return $item % 2 == 0;
        });
        self::assertInstanceOf(Collection::class, $collection);
        self::assertEquals([1,3], $collection->values());
    }

    /**
     * @param  iterable $iterable
     * @dataProvider iterableProvider
     */
    public function testRejectHelperCanFilterAnyIterable($iterable)
    {
        self::assertEquals([1, 3], ch\reject($iterable, function ($item) {
            return $item % 2 == 0;
        })->values());
    }

    public function testReduceReturnsAReducedValue()
    {
        $reduced = ch\reduce([1,2,3,4], function ($accumulator, $item) {
            return $accumulator + $item;
        }, 0);
        self::assertEquals(10, $reduced);
    }

    /**
     * @param  iterable $iterable
     * @dataProvider iterableProvider
     */
    public function testReduceHelperCanReduceAnyIterable($iterable)
    {
        self::assertEquals(10, ch\reduce($iterable, function ($accumulator, $item) {
            return $accumulator + $item;
        }, 0));
    }

    /**
     * @param  iterable $iterable1
     * @param  iterable $iterable2
     * @param  iterable $iterable3
     * @dataProvider mergeIterableProvider
     */
    public function testMergeHelperCanMergeAnyIterable($iterable1, $iterable2, $iterable3)
    {
        self::assertEquals([1, 2, 3, 4, 5, 6], ch\merge($iterable1, $iterable2, $iterable3)->values());
    }

    public function iterableProvider()
    {
        return [
            'Array' => [[1, 2, 3, 4]],
            'Collection' => [Collection::from([1, 2, 3, 4])],
            'GeneratorCollection' => [GeneratorCollection::from([1, 2, 3, 4])],
            'ArrayAccess' => [new \ArrayIterator([1, 2, 3, 4])],
            'Iterator' => [new \IteratorIterator(new \ArrayIterator([1, 2, 3, 4]))],
        ];
    }

    public function mergeIterableProvider()
    {
        return [
            [[1, 2], [3, 4], [5, 6]],
            [[1, 2], Collection::from([3, 4]), GeneratorCollection::from([5, 6])],
            [GeneratorCollection::from([1, 2]), new \ArrayIterator([3, 4]), [5, 6]],
            [new \IteratorIterator(new \ArrayIterator([1, 2])), [3, 4], Collection::from([5, 6])],
        ];
    }
}
