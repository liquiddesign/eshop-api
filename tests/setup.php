<?php

require_once __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');
const TMP_DIR = '/temp/tests';

function test(string $description, Closure $fn): void
{
	echo $description, "\n";
	$fn();
}
