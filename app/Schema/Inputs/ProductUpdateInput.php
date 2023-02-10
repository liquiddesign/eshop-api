<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Product;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\TypeRegister;

class ProductUpdateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createCrudUpdateInputFieldsFromClass(Product::class),
		];

		parent::__construct($config);
	}
}
