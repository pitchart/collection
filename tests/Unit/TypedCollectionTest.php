<?php

namespace Pitchart\Collection\Test\Unit;

use Pitchart\Collection\Collection;
use Pitchart\Collection\TypedCollection;

class TypedCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInstantiated()
    {
        $collection = new TypedCollection(array(), 'stdClass');
        self::assertInstanceOf(TypedCollection::class, $collection);
        self::assertInstanceOf(Collection::class, $collection);
        self::assertInstanceOf(\Countable::class, $collection);
        self::assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testCanBeBuilded()
    {
        $collection = TypedCollection::from(array(new \stdClass()));
        self::assertInstanceOf(TypedCollection::class, $collection);
        self::assertInstanceOf(Collection::class, $collection);
        self::assertInstanceOf(\Countable::class, $collection);
        self::assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testThrowsExceptionIfAnItemIsNotOfSameTypeInBuilder()
    {
        self::expectException(\InvalidArgumentException::class);
        $collection = TypedCollection::from(array(new \stdClass(), new \DateTime()));
    }

    public function testThrowsExceptionIfAnItemIsNotOfSameTypeInConstructor()
    {
        self::expectException(\InvalidArgumentException::class);
        $collection = new TypedCollection(array(new \stdClass(), new \DateTime()), 'stdClass');
    }

    public function testCanAddAnItemOfDefinedType()
    {
        $collection = new TypedCollection(array(), \DateTime::class);
        $date = new \DateTime;
        $collection->add($date);
        self::assertContains($date, $collection->toArray());
    }

    public function testThrowsAnExceptionWhenAddAnItemOfAnotherType()
    {
        self::expectException(\InvalidArgumentException::class);
        $collection = new TypedCollection(array(), \DateTime::class);
        $item = new \stdClass();
        $collection->add($item);
    }
}
