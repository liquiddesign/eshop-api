<?php

namespace App\Resolvers\Base;

use Nette\DI\Container;

abstract class BaseResolver
{
	public function __construct(protected readonly Container $container)
	{
	}
}
