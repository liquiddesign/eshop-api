<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\CustomerGroup;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class CustomerGroupOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(CustomerGroup::class),
		];

		parent::__construct($config);
	}
}
