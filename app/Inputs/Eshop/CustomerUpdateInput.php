<?php

namespace App\Inputs\Eshop;

use App\Base\BaseInput;
use App\TypeRegister;
use Eshop\DB\Customer;

class CustomerUpdateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(Customer::class, forceAllOptional: true),
		];

		parent::__construct($config);
	}
}
