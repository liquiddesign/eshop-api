<?php

declare(strict_types=1);

namespace App\Schema\Types\Eshop;

use App\Schema\Base\CrudQuery;
use Eshop\DB\Customer;

class CustomerQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Customer::class;
	}
}
