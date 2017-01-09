<?php

namespace Pitchart\Collection\Test\Functional;

use Pitchart\Collection\Collection;
use Pitchart\Collection\Test\Stub\Model\Character;

class PipelineTest extends \PHPUnit_Framework_TestCase
{

    public function testIntegerArrayPipeline()
    {
        $collection = Collection::from([1, 2, 3, 4, 5, 6]);

        $pipelineReductionSum = $collection->filter(
            function ($item) {
                return $item % 2  == 0;
            }
        )
            ->map(
                function ($item) {
                    return $item * 2;
                }
            )
            ->reduce(
                function ($accumulator, $item) {
                    return $accumulator + $item;
                },
                0
            );

        $this->assertEquals(24, $pipelineReductionSum);
    }

    public function testObjectArrayPipelines()
    {
        $collection = Collection::from($this->loadCharacterStubs());

        $emailList = $collection
            ->reject(
                function (Character $character) {
                    return (boolean) $character->getEmail() == null;
                }
            )
            ->map(
                function (Character $character) {
                    return $character->getEmail();
                }
            );
        $this->assertContains('jay@quick.stop', $emailList);
        $this->assertEquals(7, $emailList->count());

        $peopleAtQuickStop = $collection
            ->filter(
                function (Character $character) {
                    return preg_match('/@quick.stop$/', $character->getEmail());
                }
            )
            ->map(
                function (Character $character) {
                    return $character->getAge();
                }
            );
        $averageAgeAtQuickStop = $peopleAtQuickStop->reduce(
            function ($accumulator, $age) {
                return $accumulator + $age;
            },
            0
        ) / $peopleAtQuickStop->count();

        $this->assertEquals(33, $averageAgeAtQuickStop);
    }

    private function loadCharacterStubs()
    {
        return [
            new Character('Jay', 'jay@quick.stop', 35),
            new Character('Silent Bob', 'bob@quick.stop', 37),
            new Character('Dante Hicks', 'dante@quick.stop', 27),
            new Character('Randal Graves', 'randal@quick.stop', 31),
            new Character('Olaf'),
            new Character('Holden McNeil', null, 29),
            new Character('Banky Edwards', 'banky@quick.stop', 35),
            new Character('Becky', 'becky@moo.by'),
            new Character('Elias', 'elias@moo.by', 25)
        ];
    }
}
