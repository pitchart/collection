<?php

namespace Pitchart\Collection;

trait CollectionTrait
{
    /**
     * @param callable $callback
     */
    public function each(callable $callback)
    {
        foreach ($this->items as $item) {
            $callback($item);
        }
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->items));
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback)
    {
        return new static(array_filter($this->items, $callback));
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function reject(callable $callback)
    {
        return new static(array_filter($this->items, !$callback));
    }

    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial)
    {
        $accumulator = $initial;

        foreach ($this->items as $item) {
            $accumulator = $callback($accumulator, $item);
        }
        return $accumulator;
    }
}
