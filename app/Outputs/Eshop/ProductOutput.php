<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\BaseOutput;
use App\TypeRegister;
use Eshop\DB\Product;

class ProductOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		parent::__construct(['fields' => $typeRegister->createOutputFieldsFromClass(Product::class, exclude: [])]);
	}
}
