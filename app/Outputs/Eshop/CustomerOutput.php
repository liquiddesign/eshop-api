<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\BaseOutput;
use App\TypeRegister;
use Eshop\DB\Customer;

class CustomerOutput extends BaseOutput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName()),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Customer::class;
	}
}
