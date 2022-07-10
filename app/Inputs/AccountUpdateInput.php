<?php

namespace App\Inputs;

use App\Base\BaseInput;
use App\TypeRegistry;
use Security\DB\Account;

class AccountUpdateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'name' => 'AccountUpdate',
			'fields' => TypeRegistry::createFieldsFromClass($this->getSourceClassName(), exclude: ['tsRegistered', 'tsLastLogin'], forceAllOptional: true),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}
