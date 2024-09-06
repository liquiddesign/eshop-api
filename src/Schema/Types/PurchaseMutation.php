<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace EshopApi\Schema\Types;

class PurchaseMutation extends \LqGrAphi\Schema\CrudMutation
{
	public function getClass(): string
	{
		return \Eshop\DB\Purchase::class;
	}

	public function getCreateInputName(): string
	{
		return 'PurchaseCreateInput';
	}

	public function getUpdateInputName(): string
	{
		return 'PurchaseUpdateInput';
	}
}
