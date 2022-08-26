<?php

namespace App\Inputs\Eshop;

use App\Base\BaseInput;
use App\Inputs\InputRelationFieldsEnum;
use App\TypeRegister;
use Eshop\DB\Customer;

class CustomerCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(Customer::class, includeId: false, inputRelationFieldsEnum: InputRelationFieldsEnum::ONLY_ADD),

		];

		parent::__construct($config);
	}
}
