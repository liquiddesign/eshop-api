<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;
use Eshop\DB\CustomerRepository;

class CustomerQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'customer';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegister::customer();
	}

	public function getRepositoryClass(): string
	{
		return CustomerRepository::class;
	}
}
