<?php

namespace App\Resolvers;

use App\Resolvers\Base\CrudResolver;
use Eshop\DB\Customer;

class CustomerResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
