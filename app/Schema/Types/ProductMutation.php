<?php

namespace EshopApi\Schema\Types;

use Eshop\DB\Product;
use LqGrAphi\Schema\CrudMutation;

class ProductMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Product::class;
	}
}
