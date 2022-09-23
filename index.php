<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

// to maintanace on rename .maintenance.php to maintenance.php
if (\is_file($maintenance = __DIR__ . '/maintenance.php')) {
	require $maintenance;
}

$container = \App\Bootstrap::boot()->createContainer();

$graphql = $container->getByType(\App\GraphQL::class);
$request = $container->getByType(\Nette\Http\Request::class);
$response = $container->getByType(\Nette\Http\Response::class);

if ($graphql->getDebugFlag() && $request->getMethod() === 'GET') {
	/** @var \Nette\Bridges\ApplicationLatte\LatteFactory $latteFactory */
	$latteFactory = $container->getByType(\Nette\Bridges\ApplicationLatte\LatteFactory::class);

	$compiledSandbox = $latteFactory->create()->renderToString(__DIR__ . '/apollo.sandbox.latte', ['baseUrl' => $request->getUrl()->getBaseUrl()]);

	(new \Nette\Application\Responses\TextResponse($compiledSandbox))->send($request, $response);

	die;
}

(new \Nette\Application\Responses\JsonResponse(
	$graphql->executeServer()
))->send($request, $response);
