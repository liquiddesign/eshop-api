<?php

namespace App\Schema\Inputs\Eshop;

use App\Schema\Base\BaseInput;
use App\Schema\Inputs\InputRelationFieldsEnum;
use App\Schema\TypeRegister;
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
