<?php

namespace Pitchart\Collection;

/**
 * Describes the basic interface for checks on a collection's elements
 *
 * @author Julien VITTE <vitte.julien@gmail.com>
 */
interface Checkable extends CollectionInterface
{
    /**
     * Returns true if all items satisfy the callable condition
     *
     * @param callable $callable The callable takes a collection element as parameter and returns a boolean
     *
     * @return boolean
     */
    public function every(callable $callable);

    /**
     * Returns true if at least one item satisfies the callable condition
     *
     * @param callable $callable The callable takes a collection element as parameter and returns a boolean
     *
     * @return boolean
     */
    public function some(callable $callable);

    /**
     * Returns true if no item satisfies the callable condition
     *
     * @param callable $callable The callable takes a collection element as parameter and returns a boolean
     *
     * @return boolean
     */
    public function none(callable $callable);
}
