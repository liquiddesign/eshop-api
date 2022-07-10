<?php

declare(strict_types=1);

namespace App\Outputs\Security;

use App\Base\BaseOutput;
use App\TypeRegister;
use Security\DB\Account;

class AccountOutput extends BaseOutput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName(), exclude: ['password']),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}
}
