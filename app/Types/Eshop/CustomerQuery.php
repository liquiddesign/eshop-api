<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Crud\CrudQuery;
use Eshop\DB\CustomerRepository;

class CustomerQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'customer';
	}

	public function getRepositoryClass(): string
	{
		return CustomerRepository::class;
	}
}
