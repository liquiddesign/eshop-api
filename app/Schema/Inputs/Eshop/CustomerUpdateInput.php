<?php

namespace App\Schema\Inputs\Eshop;

use App\Schema\Base\BaseInput;
use App\Schema\TypeRegister;
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
