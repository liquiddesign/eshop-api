<?php

namespace App\Types\Eshop;

use App\Crud\CrudMutation;
use Eshop\DB\CustomerRepository;

class CustomerMutation extends CrudMutation
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
