<?php

namespace Pitchart\Collection;

class GeneratorCollection extends \IteratorIterator
{
    public static function from($iterable) {
        if (is_array($iterable)
            || $iterable instanceof \IteratorAggregate
        ) {
            return new static(new \ArrayIterator($iterable));
        }
        if ($iterable instanceof \Iterator) {
            return new static($iterable);
        }

        throw new \InvalidArgumentException(
            sprintf(
                'Argument 1 must be an instance of Traversable or an array, %s given',
                is_object($iterable) ? get_class($iterable) : gettype($iterable)
            )
        );
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return iterator_to_array($this->getInnerIterator());
    }

    /**
     * @return Collection
     */
    public function persist()
    {
        return new Collection($this->toArray());
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function map(callable $callable)
    {
        $mapping = function ($iterator) use ($callable) {
            foreach ($iterator as $key => $item) {
                yield $key => $callable($item);
            }
        };
        return new static($mapping($this->getInnerIterator()));
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function filter(callable $callable)
    {
        $filtering = function ($iterator) use ($callable) {
            foreach ($iterator as $key => $item) {
                if ($callable($item)) {
                    yield $item;
                }
            }
        };
        return new static($filtering($this->getInnerIterator()));
    }

    /**
     * @param callable $callable
     * @return GeneratorCollection
     */
    public function select(callable $callable)
    {
        return $this->filter($callable);
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function reject(callable $callable)
    {
        //$function = $this->normalizeAsCallables($callable);
        $rejecting = function ($iterator) use ($callable) {
            foreach ($iterator as $key => $item) {
                if (!$callable($item)) {
                    yield $item;
                }
            }
        };
        return new static($rejecting($this->getInnerIterator()));
    }

    /**
     * @param Collection $collection
     * @return static
     */
    public function merge(self $collection)
    {
        $merging = function (\Iterator $iterator1, \Iterator $iterator2) {
            foreach ($iterator1 as $item) {
                yield $item;
            }
            foreach ($iterator2 as $item) {
                yield $item;
            }
        };
        return new static($merging($this->getInnerIterator(), $collection));
    }

    /**
     * @param callable $callable
     * @param mixed    $initial
     * @return mixed
     */
    public function reduce(callable $callable, $initial)
    {
        $accumulator = $initial;
        //$function = $this->normalizeAsCallables($callable);

        foreach ($this->getInnerIterator() as $item) {
            $accumulator = $callable($accumulator, $item);
        }
        return $accumulator;
    }

}