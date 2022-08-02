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
			'fields' => $typeRegister->createInputFieldsFromClass($this->getSourceClassName(), forceAllOptional: true, includeId: false),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Customer::class;
	}
}
