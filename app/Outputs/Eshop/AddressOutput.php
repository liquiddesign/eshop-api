<?php

declare(strict_types=1);

namespace App\Outputs\Eshop;

use App\Base\BaseOutput;
use App\TypeRegister;
use Eshop\DB\Address;

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
