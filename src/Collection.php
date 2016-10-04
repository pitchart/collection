<?php

namespace Pitchart\Collection;

class Collection implements \ArrayAccess, \Countable {

	protected $items;

	public function __construct(array $items)
	{
		$this->items = $items;
	}

	public static function from(array $items)
	{
		return new static($items);
	}

	public function each($callback)
	{
		foreach ($this->items as $item) {
			$callback($item);
		}
	}

	public function map($callback)
	{
		return new static(array_map($callback, $this->items));
	}

	public function filter($callback)
	{
		return new static(array_filter($this->items, $callback));
	}

	public function reject($callback)
	{
		return new static(array_filter($this->items, !$callback));
	}

	public function reduce($callback, $initial)
	{
		$accumulator = $initial;

		foreach ($this->items as $item) {
			$accumulator = $callback($accumulator, $item);
		}
		return $accumulator;
	}

	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->items);
	}

	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}

	public function offsetSet($offset, $value)
	{
		if ($offset === null) {
			$this->items[] = $value;
		}
		else {
			$this->items[$offset] = $value;
		}
	}

	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	public function count()
	{
		return count($this->items);
	}

	public function toArray()
	{
		return $this->items;
	}


}
