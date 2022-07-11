<?php

namespace App\Inputs\Security;

use App\Base\BaseInput;
use App\TypeRegister;
use Security\DB\Account;

class AccountUpdateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName(), exclude: ['tsRegistered', 'tsLastLogin'], forceAllOptional: true),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}