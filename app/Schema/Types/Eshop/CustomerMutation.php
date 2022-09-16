<?php

namespace App\Schema\Types\Eshop;

use App\Schema\Base\CrudMutation;
use Eshop\DB\Customer;

class CustomerMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
