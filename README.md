React Settle Promise
--------------------

Settle function for [React\Promise](https://reactphp.org/promise/).

Usage
=====

```php
use CubeTools\React\SettlePromise;

$promises = [
    React\Promise\resolve('foo'),
    React\Promise\reject(new \Exception()),
    'bar'
];

SettlePromise\settle($promises)->then(function(array $states) {
    foreach($states as $state) {
        if (SettlePromise\FULFILLED === $state['state']) {
            $promiseValue = $state['value'];
            ...
        } else { // SettlePromise\REJECTED
            $failureReason = $state['reason'];
            ...
        }
    }
});
```

```php
use CubeTools\React\SettlePromise;
use Clue\React\Block;

$loop = \React\EventLoop\Factory::create();

foreach(Block\await(SettlePromise\settleWithTimeout($promises, 5, $loop), $loop) {
    if (SettlePromise\FULFILLED === $state['state']) {
        $promiseValue = $state['value'];
        ...
    } else { // SettlePromise\REJECTED
        $failureReason = $state['reason'];
        ...
    }
}

```

To replace the constants, subclass \CubeTools\React\SettlePromise\SettlePromise and define the class constants FULFILLED and REJECTED.
Then use the class functions like `YourClass::settle(...)`.

Installation
============

Step 1: Download the Package
----------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this package:

```console
$ composer require cubetools/react-settle-promise
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.
