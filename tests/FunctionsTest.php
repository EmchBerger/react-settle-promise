<?php

namespace Tests\CubeTools\ReactSettlePromise;

use CubeTools\React\SettlePromise;
use PHPUnit\Framework\TestCase;
use React\Promise;

class SettleFunctionTest extends TestCase
{
    public function testConstants () {
        $this->assertSame('fulfilled', SettlePromise\FULFILLED);
        $this->assertSame('rejected', SettlePromise\REJECTED);
    }

    public function testSettle()
    {
        $called = 0;
        $promises = [];

        $sp = SettlePromise\settle($promises)->then(function (array $states) use (&$called) {
            ++$called;
        });

        $this->assertSame(1, $called, "settle->then called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $sp);
    }

    public function testSettleWithTimeout() {
        $called = 0;
        $promises = [];

        $timeout = 0.02;
        $loop = \React\EventLoop\Factory::create();;

        $sp = SettlePromise\settleWithTimeout($promises, $timeout, $loop)->then(function (array $states) use (&$called) {
            ++$called;
        });

        $this->assertSame(1, $called, "settleWithTimeout->then called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $sp);
    }

}
