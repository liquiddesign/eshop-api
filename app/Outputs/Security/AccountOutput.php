<?php

declare(strict_types=1);

namespace App\Outputs\Security;

use App\Base\BaseOutput;
use App\TypeRegister;
use Security\DB\Account;

class AccountOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Account::class, exclude: ['password']),
		];

		parent::__construct($config);
	}
}
