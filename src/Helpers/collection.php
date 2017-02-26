<?php

namespace Pitchart\Collection\Helpers;

use Pitchart\Collection\Collection;
use Pitchart\Collection\GeneratorCollection;

function collect($items)
{
	return Collection::from($items);
}

function generator($items)
{
	return GeneratorCollection::from($items);
}