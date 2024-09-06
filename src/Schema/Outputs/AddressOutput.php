<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\Address;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class AddressOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Address::class),
		];

		parent::__construct($config);
	}
}
