<?php

declare(strict_types=1);

namespace App\Types\Security;

use App\Crud\CrudQuery;
use Security\DB\Account;

class AccountQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Account::class;
	}
}
