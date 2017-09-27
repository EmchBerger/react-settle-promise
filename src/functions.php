<?php

namespace CubeTools\React\SettlePromise;

/**
 * state when promise element fulfilled
 */
const FULFILLED = 'fulfilled';

/**
 * state when promise element rejected
 */
const REJECTED = 'rejected';

/**
 *
 * @see SettlePromise::settle
 */
function settle($promisesOrValues)
{
    return SettlePromise::settle($promisesOrValues);
}

/**
 *
 * @see SettlePromise::settleWithTimeout
 */

function settleWithTimeout($promisesOrValues, $timeout, $loop)
{
    return SettlePromise::settleWithTimeout($promisesOrValues, $timeout, $loop);
}


