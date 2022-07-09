<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

// to maintanace on rename .maintenance.php to maintenance.php
if (\is_file($maintenance = __DIR__ . '/maintenance.php')) {
	require $maintenance;
}

\App\Bootstrap::boot()
	->createContainer()
	->getByType(\App\Application::class)
	->run();
