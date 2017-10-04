<?php

namespace Tests\CubeTools\React\SettlePromise;

use CubeTools\React\SettlePromise\SettleBoolean;
use PHPUnit\Framework\TestCase;
use React\Promise;

class SettleBooleanTest extends TestCase
{
    public function testConstants () {
        $this->assertSame(true, SettleBoolean::FULFILLED);
        $this->assertSame(false, SettleBoolean::REJECTED);
    }


    public function testSettle()
    {
        $called = 0;
        $thenStates = null;
        $promises = [
            Promise\resolve('Resolved'),
            Promise\reject(new \Exception(2)),
        ];

        $sp = SettleBoolean::settle($promises)->then(function (array $states) use (&$called, &$thenStates) {
            $thenStates = $states;
            ++$called;
        });

        $this->assertSame(1, $called, "settle->then called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $sp);
        $this->assertSame([true, false], array_map(function ($value) {
            return $value['state'];
        }, $thenStates));
    }

    public function testSettleWithTimeout() {
        $called = 0;
        $thenStates = null;
        $promises = [
            Promise\reject(new \Exception(1)),
            Promise\resolve(2),
        ];

        $timeout = 0.02;
        $loop = \React\EventLoop\Factory::create();;

        $sp = SettleBoolean::settleWithTimeout($promises, $timeout, $loop)->then(
            function (array $states) use (&$called, &$thenStates) {
                $thenStates = $states;
                ++$called;
            }
        );

        $this->assertSame(1, $called, "settleWithTimeout->then called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $sp);
        $this->assertSame([false, true], array_map(function ($value) {
            return $value['state'];
        }, $thenStates));
    }
}
