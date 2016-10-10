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
    public function values()
    {
        return $this->getArrayCopy();
    }

    /**
     * Execute a callback function on each item
     *
     * @param \Closure $function
     */
    public function each(\Closure $function)
    {
        foreach ($this->getArrayCopy() as $item) {
            $function($item);
        }
    }

    /**
     * Map a function over a collection
     *
     * @param \Closure $function
     * @return static
     */
    public function map(\Closure $function)
    {
        return new static(array_map($function, $this->getArrayCopy()));
    }

    /**
     * @param \Closure $function
     * @return static
     */
    public function filter(\Closure $function)
    {
        return new static(array_filter($this->getArrayCopy(), $function));
    }

    /**
     * Alias for filter()
     * @see filter()
     *
     * @param \Closure $function
     * @return static
     */
    public function select(\Closure $function)
    {
        return self::filter($function);
    }

    /**
     * @param \Closure $function
     * @return static
     */
    public function reject(\Closure $function)
    {
        return new static(array_filter($this->getArrayCopy(), function ($item) use ($function) {
            return !$function($item);
        }));
    }

    /**
     * Remove duplicate elements
     *
     * @return static
     */
    public function distinct() {
        return new static(array_unique($this->values()));
    }

    /**
     * 
     * @param \Closure $function
     * @return static
     */
    public function sort(\Closure $function)
    {
        $sorted = $this->values();
        usort($sorted, $function);
        return new static($sorted);
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
        return new static(array_values(array_diff($this->values(), $collection->values())));
    }

    /**
     * @param Collection $collection
     * @return static
     */
    public function intersection(self $collection)
    {
        return new static(array_values(array_intersect($this->values(), $collection->values())));
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
     * Group a collection using a \Closure
     */
    public function groupBy(\Closure $groupBy, $preserveKeys = false)
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
    public function concat()
    {
        return $this->reduce(function (Collection $accumulator, $item) {
            if ($item instanceof Collection) {
                $accumulator = $accumulator->merge($item);
            }
            return $accumulator;
        }, new static([]));
    }

    /**
     * Map a function over a collection and flatten the result by one-level
     *
     * @param \Closure $function
     * @return static
     */
    public function flatMap(\Closure $function)
    {
        return $this->map($function)->concat();
    }

    /**
     * Alias for flatMap()
     *
     * @see flatMap
     */
    public function mapcat(\Closure $function)
    {
        return $this->flatMap($function);
    }

    /**
     * @param \Closure $function
     * @param mixed $initial
     * @return mixed
     */
    public function reduce(\Closure $function, $initial)
    {
        $accumulator = $initial;

        foreach ($this->getArrayCopy() as $item) {
            $accumulator = $function($accumulator, $item);
        }
        return $accumulator;
    }
}
