<?php

namespace EshopApi;

use Nette\Configurator;
use Security\DB\Account;

class Scripts extends \Base\Scripts
{
	protected static function createConfigurator(): Configurator
	{
		/** @var \Nette\Configurator $configurator */
		$configurator = Bootstrap::boot();

		return $configurator;
	}
	
	protected static function getAccountEntityClass(): string
	{
		return Account::class;
	}
	
	protected static function getRootDirectory(): string
	{
		return __DIR__ . '/..';
	}
}
