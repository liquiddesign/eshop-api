<?php

namespace EshopApi\Schema\Inputs;

use Eshop\DB\Product;
use LqGrAphi\Schema\BaseInput;
use LqGrAphi\Schema\TypeRegister;

class ProductCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createCrudCreateInputFieldsFromClass(Product::class),
		];

		parent::__construct($config);
	}
}
