<?php

namespace Tests\CubeTools\React\SettlePromise;

use CubeTools\React\SettlePromise\SettlePromise;
use PHPUnit\Framework\TestCase;
use React\Promise;

class SettlePromiseTest extends TestCase
{
    public function testConstants () {
        $this->assertSame('fulfilled', SettlePromise::FULFILLED);
        $this->assertSame('rejected', SettlePromise::REJECTED);
    }

    public function testSettle()
    {
        $called = 0;
        $thenStates = null;
        $ex = new \Exception('some error message');
        $promises = [
            Promise\resolve('foo'),
            Promise\reject($ex),
            'bar',
        ];

        $expected = [
            ['state' => 'fulfilled', 'value' => 'foo'],
            ['state' => 'rejected', 'reason' => $ex],
            ['state' => 'fulfilled', 'value' => 'bar'],
        ];
        $s = SettlePromise::settle($promises)->then(function (array $states) use (&$thenStates, &$called) {
            ++$called;
            $thenStates = $states;
        });

        $this->assertSame(1, $called, "settle called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $s);
        $this->assertSameStates($expected, $thenStates);
    }

    public function testSettleNamed() {
        $thenStates = null;
        $ex = new \Exception('some error in '.__METHOD__);
        $promises = [
            7 => 'bAr',
            'f' => Promise\resolve('fOo'),
            'ke' => Promise\reject($ex),
            null,
        ];

        $expected = [
            7 => ['state' => 'fulfilled', 'value' => 'bAr'],
            'f' => ['state' => 'fulfilled', 'value' => 'fOo'],
            'ke' => ['state' => 'rejected', 'reason' => $ex],
            ['state' => 'fulfilled', 'value' => null],
        ];
        $s = SettlePromise::settle($promises)->then(function (array $states) use (&$thenStates, &$called) {
            ++$called;
            $thenStates = $states;
        });

        $this->assertSame(1, $called, "settle called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $s);
        $this->assertSameStates($expected, $thenStates);
    }

    public function testSettleNone() {
        $thenStates = null;
        $ex = new \Exception('some error in '.__METHOD__);
        $promises = [];
        $expected = [];
        $s = SettlePromise::settle($promises)->then(function (array $states) use (&$thenStates, &$called) {
            ++$called;
            $thenStates = $states;
        });

        $this->assertSame(1, $called, "settle called once");
        $this->assertInstanceOf(Promise\FulfilledPromise::class, $s);
        $this->assertSameStates($expected, $thenStates);
    }

    public function testSettleWithTimeout()
    {
        $called = 0;
        $thenStates = null;

        $timeout = 0.02;
        $delayPass = $timeout / 2;
        $loop = \React\EventLoop\Factory::create();

        $promises = [
            Promise\resolve('baR'),
            Promise\reject(new \Exception('error in timeout')),
            Promise\Timer\resolve($delayPass, $loop),
            Promise\Timer\resolve(3 * $timeout, $loop),
            'FOO'
        ];

        $expected = [
            ['state' => 'fulfilled', 'value' => 'baR'],
            ['state' => 'rejected', 'reason' => new \Exception('error in timeout')],
            ['state' => 'fulfilled', 'value' => $delayPass],
            ['state' => 'rejected', 'reason' => new Promise\Timer\TimeoutException($timeout, "Timed out after $timeout seconds")],
            ['state' => 'fulfilled', 'value' => 'FOO'],
        ];

        $s = SettlePromise::settleWithTimeout($promises, $timeout, $loop)->then(function (array $states) use (&$thenStates, &$called) {
            ++$called;
            $thenStates = $states;
        });

        $loop->run();

        $this->assertSame(1, $called, "settle called once");
        $this->assertNotInstanceOf(Promise\RejectedPromise::class, $s); // is Promise\Promise
        $this->assertSameStates($expected, $thenStates);
    }

    protected function assertSameStates($expected, $received, $msg = null)
    {
        if ($expected === $received) {
            $this->assertTrue(true); // is same
        } elseif ($expected == $received) {
            $this->assertSame($expected, $received, $msg);
        } else { // assertEquals for nicer error message
            $this->assertEquals($expected, $received, $msg);
        }
    }
}
