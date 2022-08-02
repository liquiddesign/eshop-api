<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\BaseOutput;
use App\TypeRegister;
use Eshop\DB\CustomerGroup;

class CustomerGroupOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass($this->getSourceClassName()),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return CustomerGroup::class;
	}
}
