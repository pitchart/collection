<?php

namespace Pitchart\Collection;

class Collection implements \ArrayAccess, \Countable {

	protected $items;

	/**
	 * @param array $items
	 */
	public function __construct(array $items)
	{
		$this->items = $items;
	}

	/**
	 * @param array $items
	 * @return static
	 */
	public static function from(array $items)
	{
		return new static($items);
	}

	/**
	 * @param callable $callback
	 */
	public function each(callable $callback)
	{
		foreach ($this->items as $item) {
			$callback($item);
		}
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function map(callable $callback)
	{
		return new static(array_map($callback, $this->items));
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function filter(callable $callback)
	{
		return new static(array_filter($this->items, $callback));
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function reject(callable $callback)
	{
		return new static(array_filter($this->items, !$callback));
	}

	/**
	 * @param callable $callback
	 * @param mixed $initial
	 * @return mixed
	 */
	public function reduce(callable $callback, $initial)
	{
		$accumulator = $initial;

		foreach ($this->items as $item) {
			$accumulator = $callback($accumulator, $item);
		}
		return $accumulator;
	}

	/**
	 * @param mixed $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return array_key_exists($offset, $this->items);
	}

	/**
	 * @param mixed $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->items[$offset];
	}

	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		if ($offset === null) {
			$this->items[] = $value;
		}
		else {
			$this->items[$offset] = $value;
		}
	}

	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	/**
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->items;
	}


}
