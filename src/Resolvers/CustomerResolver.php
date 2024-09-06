<?php

namespace EshopApi\Resolvers;

use Eshop\DB\Customer;
use LqGrAphi\Resolvers\CrudResolver;

class CustomerResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
