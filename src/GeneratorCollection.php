<?php

namespace Pitchart\Collection;

use Pitchart\Collection\Mixin\CallableUnifierTrait;

class GeneratorCollection extends \IteratorIterator
{

    use CallableUnifierTrait;

    public static function from($iterable)
    {
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
    public function persist($class = Collection::class)
    {
        return new $class($this->toArray());
    }

    /**
     * @param callable $callable
     * @return static
     */
    public function map(callable $callable)
    {
        $function = $this->normalizeAsCallables($callable);
        $mapping = function ($iterator) use ($function) {
            foreach ($iterator as $key => $item) {
                yield $key => $function($item);
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
        $function = $this->normalizeAsCallables($callable);
        $filter = function ($iterator) use ($function) {
            foreach ($iterator as $key => $item) {
                if ($function($item)) {
                    yield $item;
                }
            }
        };
        return new static($filter($this->getInnerIterator()));
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
        $function = $this->normalizeAsCallables($callable);
        $rejection = function ($iterator) use ($function) {
            foreach ($iterator as $key => $item) {
                if (!$function($item)) {
                    yield $item;
                }
            }
        };
        return new static($rejection($this->getInnerIterator()));
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
        $function = $this->normalizeAsCallables($callable);

        foreach ($this->getInnerIterator() as $item) {
            $accumulator = $function($accumulator, $item);
        }

        return $accumulator;
    }
}
