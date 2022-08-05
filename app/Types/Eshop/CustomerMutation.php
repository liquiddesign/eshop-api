<?php

namespace App\Types\Eshop;

use App\Crud\CrudMutation;
use Eshop\DB\Customer;

class CustomerMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
