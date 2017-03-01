<?php

namespace Pitchart\Collection;

/**
 * Describes the minimal interface for a collection
 *
 * @author Julien VITTE <vitte.julien@gmail.com>
 */
interface CollectionInterface
{
    /**
     * Returns items as array
     *
     * @return array
     */
    public function toArray();

    /**
     * Return reindexed items as array
     *
     * @return array
     */
    public function values();

    /**
     * Applies a callable to the elements of the collection
     *
     * @param  callable $callable The callable takes a collection element as parameter and returns a transformed result
     * @return self
     */
    public function map(callable $callable);

    /**
     * Filters elements of a collection using a callable
     *
     * @param callable $callable The callable takes a collection element as parameter and returns a boolean
     * @return self
     */
    public function filter(callable $callable);

    /**
     * Rejects elements of a collection using a callable
     *
     * @param callable $callable The callable takes a collection element as parameter and returns a boolean
     * @return self
     */
    public function reject(callable $callable);

    /**
     * Merge one or more collections
     *
     * @param Collection ...$collections
     * @return self
     */
    public function merge(...$collections);

    /**
     * Iteratively reduce a collection to a single value using a callable
     *
     * @param callable $callable The callable takes an accumulator and a collection element as parameters and returns a values which will be the accumulator of the next iteration
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callable, $initial);
}
