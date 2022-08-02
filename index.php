<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

// to maintanace on rename .maintenance.php to maintenance.php
if (\is_file($maintenance = __DIR__ . '/maintenance.php')) {
	require $maintenance;
}

$container = \App\Bootstrap::boot()
	->createContainer();

(new \Nette\Application\Responses\JsonResponse(
	$container->getByType(\App\GraphQL::class)
	->executeServer())
)->send($container->getByType(\Nette\Http\Request::class), $container->getByType(\Nette\Http\Response::class));
