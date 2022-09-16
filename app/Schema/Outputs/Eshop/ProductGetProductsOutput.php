<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Eshop;

use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;

class ProductGetProductsOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		/** @var \App\Schema\Outputs\Eshop\ProductOutput $productOutput */
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
