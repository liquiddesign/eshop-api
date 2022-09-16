<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Security;

use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;
use Security\DB\Account;

class AccountOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		parent::__construct(['fields' => $typeRegister->createOutputFieldsFromClass(Account::class, exclude: ['password'])]);
	}
}
