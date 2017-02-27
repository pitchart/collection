<?php

namespace Pitchart\Collection\Helpers;

use Pitchart\Collection\CollectionInterface;
use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

/**
 * Get a Collection from an iterable list
 *
 * @param  iterable $items
 *
 * @return Collection
 */
function collect($items)
{
    return Collection::from($items);
}

/**
 * Get a Collection from an iterable list
 *
 * @param  iterable $items
 *
 * @return GeneratorCollection
 */
function generator($items)
{
    return GeneratorCollection::from($items);
}

/**
 * Applies a callable to the elements of an iterable list and returns a Collection
 *
 * @param  iterable $items
 * @param  callable $callable The callable takes an element as parameter and returns a transformed result
 *
 * @return CollectionInterface
 */
function map($items, $callable)
{
    if (is_array($items)
        || !($items instanceof CollectionInterface)
    ) {
        $items = Collection::from($items);
    }
    return $items->map($callable);
}

/**
 * Filters elements of an iterable list and returns a Collection
 *
 * @param  iterable $items
 * @param  callable $callable The callable takes an element as parameter and returns a boolean
 *
 * @return CollectionInterface
 */
function filter($items, $callable)
{
    if (is_array($items)
        || !($items instanceof CollectionInterface)
    ) {
        $items = Collection::from($items);
    }
    return $items->filter($callable);
}

/**
 * Rejects elements of an iterable list and returns a Collection
 *
 * @param  iterable $items
 * @param  callable $callable The callable takes an element as parameter and returns a boolean
 *
 * @return CollectionInterface
 */
function reject($items, $callable)
{
    if (is_array($items)
        || !($items instanceof CollectionInterface)
    ) {
        $items = Collection::from($items);
    }
    return $items->reject($callable);
}

/**
 * Merge one or more iterables and returns a collection
 *
 * @param iterable $iterables
 *
 * @return CollectionInterface
 */
function merge(...$iterables)
{
    foreach ($iterables as $position => $iterable) {
        if (is_array($iterable)
            || !($iterable instanceof CollectionInterface)
        ) {
            $iterables[$position] = Collection::from($iterable);
        }
    }
    /** @var CollectionInterface $first */
    $first = array_shift($iterables);
    return $first->merge(...$iterables);
}

/**
 * Iteratively reduce an iterable to a single value using a callable
 *
 * @param callable $callable The callable takes an accumulator and an iterable element as parameters and returns a values which will be the accumulator of the next iteration
 * @param mixed    $initial
 *
 * @return mixed
 */
function reduce($items, $callable, $initial)
{
    if (is_array($items)
        || !($items instanceof CollectionInterface)
    ) {
        $items = Collection::from($items);
    }
    return $items->reduce($callable, $initial);
}
