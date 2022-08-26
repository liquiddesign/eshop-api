<?php

namespace App\Inputs\Security;

use App\Base\BaseInput;
use App\Inputs\InputRelationFieldsEnum;
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
				inputRelationFieldsEnum: InputRelationFieldsEnum::ONLY_ADD,
			),
		];

		parent::__construct($config);
	}
}
