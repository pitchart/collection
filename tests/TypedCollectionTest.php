<?php

namespace Pitchart\Collection\Test;

use Pitchart\Collection\Collection;
use Pitchart\Collection\TypedCollection;

class TypedCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeInstantiated() {
        $collection = new TypedCollection(array(), 'stdClass');
        $this->assertInstanceOf(TypedCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(\Countable::class, $collection);
        $this->assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testCanBeBuilded() {
        $collection = TypedCollection::from(array(new \stdClass()));
        $this->assertInstanceOf(TypedCollection::class, $collection);
        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertInstanceOf(\Countable::class, $collection);
        $this->assertInstanceOf(\ArrayAccess::class, $collection);
    }

    public function testThrowsExceptionIfAnItemIsNotOfSameTypeInBuilder() {
        $this->expectException(\InvalidArgumentException::class);
        $collection = TypedCollection::from(array(new \stdClass(), new \DateTime()));
    }

    public function testThrowsExceptionIfAnItemIsNotOfSameTypeInConstructor() {
        $this->expectException(\InvalidArgumentException::class);
        $collection = new TypedCollection(array(new \stdClass(), new \DateTime()), 'stdClass');
    }


}
