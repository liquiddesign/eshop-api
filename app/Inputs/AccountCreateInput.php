<?php

namespace App\Inputs;

use App\BaseInput;
use App\TypeRegistry;
use Security\DB\Account;

class AccountCreateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'name' => 'AccountCreate',
			'fields' => TypeRegistry::createFieldsFromClass($this->getSourceClassName(), exclude: ['tsRegistered', 'tsLastLogin', 'confirmationToken', 'active', 'authorized'], includeId: false),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}
