<?php

namespace Pitchart\Collection\Test\Unit;

use Pitchart\Collection\Helpers as ch;
use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

class HelpersTest extends \PHPUnit_Framework_TestCase
{

	public function testCollectReturnsACollection() {
		$collection = ch\collect([1, 2, 3, 4]);
		self::assertInstanceOf(Collection::class, $collection);
	}

	public function testGeneratorRetursAGeneratorCollection() {
		$collection = ch\generator([1, 2, 3, 4]);
		self::assertInstanceOf(GeneratorCollection::class, $collection);
	}

}