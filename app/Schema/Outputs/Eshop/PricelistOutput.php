<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Eshop;

use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;
use Eshop\DB\Pricelist;

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
