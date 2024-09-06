<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\Product;
use GraphQL\Type\Definition\Type;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class ProductGetProductsOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		/** @var \EshopApi\Schema\Outputs\ProductOutput $productOutput */
		$productOutput = $typeRegister->getOutputType('product', Product::class);

		$fields = $productOutput->config['fields'];

		$fields += [
				'price' => Type::nonNull(Type::float()),
				'priceVat' => Type::nonNull(Type::float()),
			];

		parent::__construct([
			'fields' => $fields,
		]);
	}
}
