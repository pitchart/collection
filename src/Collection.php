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
     * Alias for getArrayCopy()
     * @return array
     * @see getArrayCopy
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Alias for getArrayCopy()
     * @return array
     * @see getArrayCopy
     */
    public function values() {
        return $this->getArrayCopy();
    }

    /**
     * Execute a callback function on each item
     *
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
        $static = new static($this->values());
        $static->uasort($callable);
        return $static;
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

	/**
	 * @param Collection $collection
	 * @return static
	 */
    public function difference(self $collection)
    {
        return new static(array_diff($this->values(), $collection->values()));
    }

	/**
	 * @param Collection $collection
	 * @return static
	 */
    public function intersection(self $collection)
    {
        return new static(array_intersect($this->values(), $collection->values()));
    }

    /**
     * @param Collection $collection
     * @return static
     */
    public function merge(self $collection)
    {
        return new static(array_merge($this->values(), $collection->values()));
    }

    /**
     * Group a collection using a callable
     */
    public function groupBy(callable $groupBy, $preserveKeys = false)
    {
        $results = [];
        foreach ($this->values() as $key => $value) {
            $groupKeys = $groupBy($value, $key);
            if (! is_array($groupKeys)) {
                $groupKeys = [$groupKeys];
            }
            foreach ($groupKeys as $groupKey) {
                if (!in_array(gettype($groupKey), ['string', 'int'])) {
                    $groupKey = (int) $groupKey;
                }
                if (! array_key_exists($groupKey, $results)) {
                    $results[$groupKey] = new static;
                }
                $results[$groupKey]->offsetSet($preserveKeys ? $key : null, $value);
            }
        }
        return new static($results);
    }

    /**
     * Concatenates collections into a single collection
     * 
     * @return static
     */
    public function concat() {
        return $this->reduce(function(Collection $accumulator, $item) {
            if ($item instanceof Collection) {
                $accumulator = $accumulator->merge($item);
            }
            return $accumulator;
        }, new static([]));
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
