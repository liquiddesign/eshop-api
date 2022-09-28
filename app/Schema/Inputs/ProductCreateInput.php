<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Product;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\InputRelationFieldsEnum;
use LqGrAphi\Schema\TypeRegister;

class ProductCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(Product::class, includeId: false, inputRelationFieldsEnum: InputRelationFieldsEnum::ONLY_ADD),

		];

		parent::__construct($config);
	}
}
