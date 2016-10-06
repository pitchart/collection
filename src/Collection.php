<?php

namespace Pitchart\Collection;

class Collection extends \ArrayObject {

	/**
	 * @param array $items
	 * @return static
	 */
	public static function from(array $items)
	{
		return new static($items);
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return $this->getArrayCopy();
	}

	/**
	 * @param callable $callback
	 */
	public function each(callable $callback)
	{
		foreach ($this->getArrayCopy() as $item) {
			$callback($item);
		}
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function map(callable $callback)
	{
		return new static(array_map($callback, $this->getArrayCopy()));
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function filter(callable $callback)
	{
		return new static(array_filter($this->getArrayCopy(), $callback));
	}

	/**
	 * Alias for filter()
	 * @see filter()
	 *
	 * @param callable $callback
	 * @return static
	 */
	public function select(callable $callback) {
		return self::filter($callback);
	}

	/**
	 * @param callable $callback
	 * @return static
	 */
	public function reject(callable $callback)
	{
		return new static(array_filter($this->getArrayCopy(), !$callback));
	}

	/**
	 * @param callable $callback
	 * @param mixed $initial
	 * @return mixed
	 */
	public function reduce(callable $callback, $initial)
	{
		$accumulator = $initial;

		foreach ($this->getArrayCopy() as $item) {
			$accumulator = $callback($accumulator, $item);
		}
		return $accumulator;
	}

}