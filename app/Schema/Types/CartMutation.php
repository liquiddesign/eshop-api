<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace EshopApi\Schema\Types;

class CartMutation extends \LqGrAphi\Schema\CrudMutation
{
	public function getClass(): string
	{
		return \Eshop\DB\Cart::class;
	}

	public function getCreateInputName(): string
	{
		return 'CartCreateInput';
	}

	public function getUpdateInputName(): string
	{
		return 'CartUpdateInput';
	}
}
