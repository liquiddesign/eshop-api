<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Customer;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\TypeRegister;

class CustomerCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createCrudCreateInputFieldsFromClass(Customer::class),

		];

		parent::__construct($config);
	}
}
