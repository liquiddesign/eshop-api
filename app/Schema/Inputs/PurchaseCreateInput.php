<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace EshopApi\Schema\Inputs;

class PurchaseCreateInput extends \LqGrAphi\Schema\BaseInput
{
	public function __construct(\LqGrAphi\Schema\TypeRegister $typeRegister)
	{
		parent::__construct([
			'fields' => $typeRegister->createCrudCreateInputFieldsFromClass(\Eshop\DB\Purchase::class),
		]);
	}
}
