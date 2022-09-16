<?php

declare(strict_types=1);

namespace App\Schema\Types\Security;

use App\Schema\Base\CrudQuery;
use Security\DB\Account;

class AccountQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Account::class;
	}
}
