<?php

namespace App\Resolvers;

use Admin\DB\Administrator;
use App\Resolvers\Base\CrudResolver;

class AdministratorResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
