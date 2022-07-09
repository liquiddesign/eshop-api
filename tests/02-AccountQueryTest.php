<?php

use Tester\Assert;

/** @var \Nette\DI\Container $container */
$container = require_once __DIR__ . '/container.php';

test('get', function () use ($container) {
	$accountRepository = $container->getByType(\Security\DB\AccountRepository::class);

	Assert::type(\Security\DB\AccountRepository::class, $accountRepository);
});
