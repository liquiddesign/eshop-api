<?php

namespace EshopApi\Schema\Types;

use Eshop\DB\Customer;
use LqGrAphi\Schema\CrudMutation;

class CustomerMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Customer::class;
	}

	public function getCreateInputName(): string
	{
		return 'CustomerCreateInput';
	}

	public function getUpdateInputName(): string
	{
		return 'CustomerUpdateInput';
	}
}
