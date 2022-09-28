<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\Customer;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class CustomerOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Customer::class, exclude: ['account']),
		];

		parent::__construct($config);
	}
}
