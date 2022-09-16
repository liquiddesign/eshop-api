<?php

namespace App\Resolvers;

use App\Resolvers\Base\CrudResolver;
use Security\DB\Account;

class AccountResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Account::class;
	}
}
