<?php

namespace App\Inputs\Security;

use App\Base\BaseInput;
use App\TypeRegister;
use Security\DB\Account;

class AccountCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(
				$this->getSourceClassName(),
				exclude: ['tsRegistered', 'tsLastLogin', 'confirmationToken', 'active', 'authorized'],
				includeId: false,
			),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}
