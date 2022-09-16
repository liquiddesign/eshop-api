<?php

namespace App\Schema\Inputs\Security;

use App\Schema\Base\BaseInput;
use App\Schema\TypeRegister;
use Security\DB\Account;

class AccountUpdateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(
				Account::class,
				exclude: ['tsRegistered', 'tsLastLogin', 'confirmationToken'],
				forceAllOptional: true,
			),
		];

		parent::__construct($config);
	}
}
