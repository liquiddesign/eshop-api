<?php

namespace App\Inputs\Eshop;

use App\Base\BaseInput;
use App\TypeRegister;
use Eshop\DB\Customer;

class CustomerCreateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName(), includeId: false),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Customer::class;
	}
}
