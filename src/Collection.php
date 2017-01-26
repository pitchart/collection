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
     *
     * @return array
     * @see    getArrayCopy
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * Return reindexed items
     *
     * @return array
     */
    public function values()
    {
        return array_values($this->getArrayCopy());
    }

    /**
     * Execute a callback function on each item
     *
     * @param callable $callable
     */
    public function each(callable $callable)
    {
        $function = $this->normalizeAsCallables($callable);
        foreach ($this->getArrayCopy() as $item) {
            $function($item);
        }
    }

    /**
     * Map a function over a collection
     *
     * @param  callable $callable
     * @return static
     */
    public function map(callable $callable)
    {
        return new static(array_map($this->normalizeAsCallables($callable), $this->getArrayCopy()));
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function filter(callable $callable)
    {
        return new static(array_filter($this->getArrayCopy(), $this->normalizeAsCallables($callable)));
    }

    /**
     * Alias for filter()
     *
     * @see filter()
     *
     * @param  callable $callable
     * @return static
     */
    public function select(callable $callable)
    {
        return self::filter($callable);
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function reject(callable $callable)
    {
        $function = $this->normalizeAsCallables($callable);
        return new static(array_filter(
            $this->getArrayCopy(),
            function ($item) use ($function) {
                return !$function($item);
            }
        ));
    }

    /**
     * Remove duplicate elements
     *
     * @return static
     */
    public function distinct()
    {
        return new static(array_unique($this->values()));
    }

    /**
     *
     * @param callable $callable
     * @return static
     */
    public function sort(callable $callable)
    {
        $sorted = $this->values();
        usort($sorted, $this->normalizeAsCallables($callable));
        return new static($sorted);
    }

    /**
     * @param int  $offset
     * @param int  $length
     * @param bool $preserveKeys
     * @return static
     */
    public function slice($offset, $length = null, $preserveKeys = false)
    {
        return new static(array_slice($this->getArrayCopy(), $offset, $length, $preserveKeys));
    }

    /**
     * @param int  $offset
     * @param int  $length
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
        return $this->reduce(
            function (Collection $accumulator, $item) {
                if ($item instanceof Collection) {
                    $accumulator = $accumulator->merge($item);
                }
                return $accumulator;
            },
            new static([])
        );
    }

    /**
     * Map a function over a collection and flatten the result by one-level
     *
     * @param  callable $callable
     * @return static
     */
    public function flatMap(callable $callable)
    {
        return $this->map($callable)->concat();
    }

    /**
     * Alias for flatMap()
     *
     * @see flatMap
     */
    public function mapcat(callable $callable)
    {
        return $this->flatMap($callable);
    }

    /**
     * Get all items but the first
     *
     * @return static
     */
    public function tail()
    {
        return new static(array_slice($this->values(), 1));
    }


    /**
     * Get the first item
     *
     * @return mixed
     */
    public function head()
    {
        $values = $this->values();
        return array_shift($values);
    }

    /**
     * @param callable $callable
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callable, $initial)
    {
        $accumulator = $initial;
        $function = $this->normalizeAsCallables($callable);

        foreach ($this->getArrayCopy() as $item) {
            $accumulator = $function($accumulator, $item);
        }
        return $accumulator;
    }

    /**
     * Returns true if all items satisfy the callable condition
     *
     * @param callable $callable
     */
    public function every(callable $callable)
    {
        $satisfies = true;
        $callable = $this->normalizeAsCallables($callable);
        foreach ($this->values() as $item) {
            if (!$callable($item)) {
                $satisfies = false;
                break;
            }
        }
        return $satisfies;
    }

    /**
     * Returns true if at least one item satisfies the callable condition
     *
     * @param callable $callable
     */
    public function some(callable $callable)
    {
        $satisfies = false;
        $callable = $this->normalizeAsCallables($callable);
        foreach ($this->values() as $item) {
            if ($callable($item)) {
                $satisfies = true;
                break;
            }
        }
        return $satisfies;
    }

    /**
     * Returns true no item satisfies the callable condition
     *
     * @param callable $callable
     */
    public function none(callable $callable)
    {
        return !$this->some($callable);
    }

    /**
     * Normalizes callbacks, closures and invokable objects calls
     *
     * @param callable $callable
     * @return callable
     */
    private function normalizeAsCallables(callable $callable)
    {
        if (is_object($callable)) {
            return $callable;
        }
        return function () use ($callable) {
            return call_user_func_array($callable, func_get_args());
        };
    }
}
