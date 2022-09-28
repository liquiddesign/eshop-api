<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class ProductGetProductsOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		/** @var \EshopApi\Schema\Outputs\ProductOutput $productOutput */
		$productOutput = $typeRegister->getOutputType('product');

		$fields = $productOutput->config['fields'];

		$fields += [
				'price' => TypeRegister::nonNull(TypeRegister::float()),
				'priceVat' => TypeRegister::nonNull(TypeRegister::float()),
			];

		parent::__construct([
			'fields' => $fields,
		]);
	}
}
