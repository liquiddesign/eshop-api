<?php

namespace EshopApi;

use Eshop\DB\Cart;
use Eshop\DB\CartItem;
use Eshop\DB\Customer;
use Eshop\DB\Product;
use Eshop\DB\Purchase;

class GeneratorScripts extends \LqGrAphi\GeneratorScripts
{
	public static function generate(\Composer\Script\Event $event): void
	{
		$types = [
			'product' => Product::class,
			'customer' => Customer::class,
			'cart' => Cart::class,
			'cartItem' => CartItem::class,
			'purchase' => Purchase::class,
		];

		self::generateOutputs($types, __DIR__ . '/Schema/Outputs', 'EshopApi\\Schema\\Outputs');
		self::generateCreateInputs($types, __DIR__ . '/Schema/Inputs', 'EshopApi\\Schema\\Inputs');
		self::generateUpdateInputs($types, __DIR__ . '/Schema/Inputs', 'EshopApi\\Schema\\Inputs');
		self::generateCrudQueries($types, __DIR__ . '/Schema/Types', 'EshopApi\\Schema\\Types');
		self::generateCrudMutations($types, __DIR__ . '/Schema/Types', 'EshopApi\\Schema\\Types');
		self::generateCrudResolvers($types, __DIR__ . '/Resolvers', 'EshopApi\\Resolvers');
	}
}
