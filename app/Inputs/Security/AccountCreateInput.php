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
				Account::class,
				exclude: ['tsRegistered', 'tsLastLogin', 'confirmationToken', 'active', 'authorized'],
				includeId: false,
			),
		];

		parent::__construct($config);
	}
}
