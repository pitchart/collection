<?php

namespace Pitchart\Collection\Mixin;

/**
 * A trait to unify callable calls syntaxes
 *
 * @author Julien VITTE <vitte.julien@gmail.com>
 */
trait CallableUnifierTrait
{
    /**
     * Normalizes callbacks, closures and invokable objects calls
     *
     * @param callable $callable
     *
     * @return callable
     */
    private function normalizeAsCallables(callable $callable)
    {
        if (is_object($callable)) {
            return $callable;
        }
        return function () use ($callable) {
            return call_user_func_array($callable, func_get_args());
        };
    }
}
