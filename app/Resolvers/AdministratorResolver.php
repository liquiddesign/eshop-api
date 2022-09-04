<?php

namespace App\Resolvers;

use Admin\DB\Administrator;
use App\Crud\CrudResolver;

class AdministratorResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
