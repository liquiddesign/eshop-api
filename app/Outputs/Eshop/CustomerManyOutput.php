<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\CollectionOutput;
use App\TypeRegister;

class CustomerManyOutput extends CollectionOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => [
				'data' => $typeRegister::nonNull($typeRegister::listOf($typeRegister->getOutputType('customer'))),
			],
		];

		parent::__construct($config, $typeRegister);
	}
}
