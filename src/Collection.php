<?php

namespace Pitchart\Collection;

class Collection extends \ArrayObject
{

    /**
     * @param array $items
     * @return static
     */
    public static function from(array $items)
    {
        return new static($items);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * @param callable $callback
     */
    public function each(callable $callback)
    {
        foreach ($this->getArrayCopy() as $item) {
            $callback($item);
        }
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->getArrayCopy()));
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback)
    {
        return new static(array_filter($this->getArrayCopy(), $callback));
    }

    /**
     * Alias for filter()
     * @see filter()
     *
     * @param callable $callback
     * @return static
     */
    public function select(callable $callback)
    {
        return self::filter($callback);
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function reject(callable $callback)
    {
        return new static(array_filter($this->getArrayCopy(), function($item) use($callback) {return !$callback($item);}));
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function sort(callable $callable)
    {
        return new static($this->uasort($callable)->toArray());
    }

    /**
     * @param int $offset
     * @param int $length
     * @param bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length, $preserveKeys));
    }

    /**
     * @param int $offset
     * @param int $length
     * @param bool $preserveKeys
     * @return static
     */
    public function take($length, $preserveKeys = false)
    {
        return $this->slice(0, $length, $preserveKeys);
    }

    public function difference(self $collection)
    {
        return new static(array_diff($this->toArray(), $collection->toArray()));
    }

    public function intersection(self $collection)
    {
        return new static(array_intersect($this->toArray(), $collection->toArray()));
    }

    /**
     * @param callable $callback
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(callable $callback, $initial)
    {
        $accumulator = $initial;

        foreach ($this->getArrayCopy() as $item) {
            $accumulator = $callback($accumulator, $item);
        }
        return $accumulator;
    }
}
