<?php

use Nette\Bootstrap\Configurator;

require_once __DIR__ . '/setup.php';

$dir = __DIR__;

$environment = (new \Nette\DI\Config\Loader())->load($dir . '/../config/environments.neon');

$configurator = new \Nette\Bootstrap\Configurator();
$configurator->setDebugMode($environment['parameters']['access']['debug'] ?? []);
$configurator->setTimeZone('Europe/Prague');

$trustedMode = $configurator->isDebugMode() || Configurator::detectDebugMode($environment['parameters']['access']['trusted']);
$debugMode = $trustedMode ? \EshopApi\Bootstrap::getDebugModeByCookie($configurator->isDebugMode()) : $configurator->isDebugMode();

$configurator->addStaticParameters([
	'trustedMode' => $trustedMode,
	'appDir' => $dir,
	'debugMode' => $debugMode,
	'productionMode' => !$debugMode,
]);

$configurator->setTempDirectory($dir . '/../temp');
$configurator->addConfig($dir . '/../config/general.neon');

if (\is_file($dir . '/../config/general.production.neon')) {
	$configurator->addConfig($dir . '/../config/general.production.neon');
} elseif (\is_file($dir . '/../config/general.local.neon')) {
	$configurator->addConfig($dir . '/../config/general.local.neon');
} else {
	\trigger_error('Please run "composer init-devel or init-production"', \E_USER_ERROR);
}

$container = $configurator->createContainer();

return $container;

