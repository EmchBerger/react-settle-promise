<?php

namespace CubeTools\React\SettlePromise;

/**
 * Settle functions return state 'true' for fulfilled, 'false' for rejected.
 */
class SettleBoolean extends SettlePromise {
    const FULFILLED = true;
    const REJECTED = false;
}
