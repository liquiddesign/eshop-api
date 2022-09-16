<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Eshop;

use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;
use Eshop\DB\Product;

class ProductOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		parent::__construct(['fields' => $typeRegister->createOutputFieldsFromClass(Product::class, exclude: [])]);
	}
}
