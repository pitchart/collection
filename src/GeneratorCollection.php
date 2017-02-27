<?php

namespace Pitchart\Collection;

use Pitchart\Collection\Mixin\CallableUnifierTrait;

/**
 * A collection using generators to perform transformations
 *
 * @author Julien VITTE <vitte.julien@gmail.com>
 */
class GeneratorCollection extends \IteratorIterator implements CollectionInterface
{

    use CallableUnifierTrait;

    /**
     * Builder for GeneratorCollection objects
     *
     * @param  iterable $iterable
     *
     * @return self
     */
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
     * @inheritDoc
     */
    public function toArray()
    {
        return iterator_to_array($this->getInnerIterator());
    }

    /**
     * @inheritDoc
     */
    public function values()
    {
        return array_values($this->toArray());
    }

    /**
     * @return Collection
     */
    public function persist($class = Collection::class)
    {
        return new $class($this->toArray());
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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
     * Alias for filter()
     *
     * @param callable $callable
     *
     * @return GeneratorCollection
     */
    public function select(callable $callable)
    {
        return $this->filter($callable);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function merge(...$collections)
    {
        $merging = function (...$iterators) {
            foreach ($iterators as $iterator) {
                foreach ($iterator as $item) {
                    yield $item;
                }
            }
        };
        return new static($merging($this->getInnerIterator(), ...$collections));
    }

    /**
     * @inheritDoc
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
