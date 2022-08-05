<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Crud\CrudQuery;
use Eshop\DB\Customer;

class CustomerQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
