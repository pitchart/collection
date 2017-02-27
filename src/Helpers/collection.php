<?php

namespace Pitchart\Collection\Helpers;

use Pitchart\Collection\CollectionInterface;
use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

/**
 * Get a Collection from an iterable list
 *
 * @param  iterable $items
 *
 * @return Collection
 */
function collect($items)
{
    return Collection::from($items);
}

/**
 * Get a Collection from an iterable list
 *
 * @param  iterable $items
 *
 * @return GeneratorCollection
 */
function generator($items)
{
    return GeneratorCollection::from($items);
}
