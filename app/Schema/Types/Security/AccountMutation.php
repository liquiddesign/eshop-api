<?php

namespace App\Schema\Types\Security;

use App\Schema\Base\CrudMutation;
use App\Schema\TypeRegister;
use Nette\DI\Container;
use Security\DB\Account;

class AccountMutation extends CrudMutation
{
	public function __construct(Container $container)
	{
		// Example of added field
		$config = [
			'fields' => [
				'accountTest' => [
					'type' => TypeRegister::nonNull(TypeRegister::string()),
					'args' => ['text' => TypeRegister::nonNull(TypeRegister::string())],
				],
			],
		];

		parent::__construct($container, $config);
	}

	public function getClass(): string
	{
		return Account::class;
	}
}
