<?php

namespace App\Schema\Inputs\Security;

use App\Schema\Base\BaseInput;
use App\Schema\Inputs\InputRelationFieldsEnum;
use App\Schema\TypeRegister;
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
