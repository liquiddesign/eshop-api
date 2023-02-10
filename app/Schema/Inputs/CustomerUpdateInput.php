<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Customer;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\TypeRegister;

class CustomerUpdateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createCrudUpdateInputFieldsFromClass(Customer::class),
		];

		parent::__construct($config);
	}
}
