<?php

namespace Pitchart\Collection;

/**
 * A collection to manage objects of the same type
 *
 * @author Julien VITTE <vitte.julien@gmail.com>
 */
class TypedCollection extends Collection
{
    /**
     * The class name required for any element of the collection
     *
     * @var string
     */
    protected $itemType;

    /**
     * @param array  $items    the items list
     * @param string $itemType the class name for the collection elements
     */
    public function __construct(array $items, $itemType)
    {
        self::validateItems($items, $itemType);

        $this->itemType = $itemType;
        parent::__construct($items);
    }

    /**
     * Builder for TypedCollection objects
     *
     * @param  iterable $iterable
     * @return TypedCollection
     */
    public static function from($iterable)
    {
        if ($iterable instanceof \Iterator) {
            $items = iterator_to_array($iterable);
        }
        if (is_array($iterable)
            || $iterable instanceof \IteratorAggregate
        ) {
            $items = (array) $iterable;
        }

        if (empty($items)) {
            throw new \InvalidArgumentException(sprintf('Can\'t build [%s] from an empty array.', static::class));
        }

        if (!is_object($items[0])) {
            throw new \InvalidArgumentException(sprintf('Invalid type [%s] for value [%s].', gettype($items[0]), $items[0]));
        }

        return new static($items, get_class($items[0]));
    }


    /**
     * @param $itemType
     * @return $this
     */
    public function setItemType($itemType)
    {
        $this->itemType = $itemType;
        return $this;
    }

    /**
     * @param object $item
     */
    public function add($item)
    {
        $validator = self::validateItem($this->itemType);
        $validator($item);
        $this->append($item);
    }

    /**
     * @param array  $items
     * @param string $itemType
     *
     * @throws \InvalidArgumentException
     */
    protected static function validateItems(array $items, $itemType)
    {
        $validateItem = static::validateItem($itemType);
        array_map(
            function ($item) use ($validateItem) {
                $validateItem($item);
            },
            $items
        );
    }

    /**
     * @param  string $type An object class name
     *
     * @return \Closure     A function to validate the data type
     * @throws \InvalidArgumentException
     */
    protected static function validateItem($type)
    {
        return function ($item) use ($type) {
            if (!is_object($item)) {
                throw new \InvalidArgumentException(sprintf('Invalid type [%s], expected [%s].', gettype($item), $type));
            }
            if (!is_a($item, $type)) {
                throw new \InvalidArgumentException(sprintf('Invalid type [%s], expected [%s].', get_class($item), $type));
            }
        };
    }
}
