<?php

namespace App\Inputs\Security;

use App\Base\BaseInput;
use App\TypeRegister;
use Security\DB\Account;

class AccountCreateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName(), exclude: ['tsRegistered', 'tsLastLogin', 'confirmationToken', 'active', 'authorized'], includeId: false),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}
