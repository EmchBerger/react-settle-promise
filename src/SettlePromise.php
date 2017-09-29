<?php

namespace CubeTools\React\SettlePromise;

use React\EventLoop\LoopInterface;
use React\Promise;
use React\Promise\Timer;

class SettlePromise {
    /**
     * state when promise element fulfilled
     */
    const FULFILLED = FULFILLED;

    /**
     * state when promise element rejected
     */
    const REJECTED = REJECTED;

    /**
     * Returns a new promise receiving an array with success states as value for ->then($value).
     *
     * The array with success states provided to then are:
     * [
     *   'state' => FULFILLED, 'value' => $valueOfPromiseElement,
     *   'state' => REJECTED, 'reason' => $rejectReasonOfPromiseElement,
     *   ...
     * }
     *
     * @param array $promisesOrValues {@link Promise\resolve}
     *
     * @return \React\Promise\PromiseInterface the new promise {@link Promise\all}
     */
    public static function settle(array $promisesOrValues)
    {
        return Promise\all(
            array_map(function($promiseOrValue) {
                return Promise\resolve($promiseOrValue)->then(
                    function ($value) {
                        return [
                            'state' => static::FULFILLED,
                            'value' => $value
                        ];
                    },
                    function ($reason) {
                        return [
                            'state' => static::REJECTED,
                            'reason' => $reason
                        ];
                    }
                );
            }, $promisesOrValues)
        );
    }

    /**
     * Returns a new promise like {@link ::settle()}, adding a timeout. Promises which timed out are reported as rejected.
     *
     * @see ::settle()
     *
     * @param array $promisesOrValues {@link ::settle()}
     * @param numeric $timeout timeout in seconds
     * @param LoopInterface $loop probably return value form {@link \React\EventLoop\Factory::create()}
     *
     * @return PromiseInterface {@link ::settle()}
     */
    public static function settleWithTimeout(array $promisesOrValues, $timeout, LoopInterface $loop)
    {
        if (!function_exists('\React\Promise\Timer\timeout')) {
            throw new \LogicException('install react/promise-timer for using '.__METHOD__);
        }

        return Promise\all(
            array_map(function($promiseOrValue) use ($timeout, $loop) {
                $promise = Timer\timeout(
                    Promise\resolve($promiseOrValue),
                    $timeout,
                    $loop
                );

                return $promise->then(
                    function ($value) {
                        return [
                            'state' => static::FULFILLED,
                            'value' => $value
                        ];
                    },
                    function ($reason) {
                        return [
                            'state' => static::REJECTED,
                            'reason' => $reason
                        ];
                    }
                );
            }, $promisesOrValues)
        );
    }
}
