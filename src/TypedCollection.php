<?php

namespace Pitchart\Collection;

class TypedCollection extends Collection
{
    protected $itemType;

    public function __construct(array $items, $itemType)
    {
        self::validateItems($items, $itemType);

        $this->itemType = $itemType;
        parent::__construct($items);
    }

    public static function from(array $items)
    {
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

    public function add($item)
    {
        $validator = self::validateItem($this->itemType);
        $validator($item);
        $this->append($item);
    }

    /**
     * @param array $items
     * @param string $itemType
     */
    protected static function validateItems(array $items, $itemType)
    {
        $validateItem = static::validateItem($itemType);
        array_map(function ($item) use ($validateItem) {
            $validateItem($item);
        }, $items);
    }

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
