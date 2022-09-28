<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Customer;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\InputRelationFieldsEnum;
use LqGrAphi\Schema\TypeRegister;

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
