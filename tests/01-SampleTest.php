<?php

use Tester\Assert;

/** @var \Nette\DI\Container $container */
$container = require_once __DIR__ . '/container.php';

test('init', function () use ($container) {
	Assert::type(\Nette\DI\Container::class, $container);
});

test('graphQL', function () use ($container) {
	Assert::type(\App\GraphQL::class, $container->getByType(\App\GraphQL::class));
});

