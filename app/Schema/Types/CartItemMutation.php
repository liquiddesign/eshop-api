<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace EshopApi\Schema\Types;

class CartItemMutation extends \LqGrAphi\Schema\CrudMutation
{
	public function getClass(): string
	{
		return \Eshop\DB\CartItem::class;
	}

	public function getCreateInputName(): string
	{
		return 'CartItemCreateInput';
	}

	public function getUpdateInputName(): string
	{
		return 'CartItemUpdateInput';
	}
}
