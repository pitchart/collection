<?php

namespace Pitchart\Collection;

interface Checkable
{
	/**
     * Returns true if all items satisfy the callable condition
     */
	public function every(callable $callable);

	/**
     * Returns true if at least one item satisfies the callable condition
     */
	public function some(callable $callable);

	/**
     * Returns true no item satisfies the callable condition
     */
	public function none(callable $callable);
}