<?php

declare(strict_types=1);

namespace EshopApi\Schema\Types;

use Eshop\DB\Customer;
use LqGrAphi\Schema\CrudQuery;

class CustomerQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
