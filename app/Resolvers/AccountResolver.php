<?php

namespace App\Resolvers;

use App\Crud\CrudResolver;
use Security\DB\Account;

class AccountResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Account::class;
	}
}
