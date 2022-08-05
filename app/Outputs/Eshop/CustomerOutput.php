<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\BaseOutput;
use App\TypeRegister;
use Eshop\DB\Customer;

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
