<?php

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

use Eshop\DB\Pricelist;
use LqGrAphi\Schema\BaseOutput;
use LqGrAphi\Schema\TypeRegister;

class PricelistOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Pricelist::class),
		];

		parent::__construct($config);
	}
}
