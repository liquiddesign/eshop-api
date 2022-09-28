<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\Product;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class ProductOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		parent::__construct(['fields' => $typeRegister->createOutputFieldsFromClass(Product::class, exclude: [])]);
	}
}
