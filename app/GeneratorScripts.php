<?php

namespace EshopApi;

use Eshop\DB\Customer;
use Eshop\DB\Product;

class GeneratorScripts extends \LqGrAphi\GeneratorScripts
{
	public static function generate(\Composer\Script\Event $event): void
	{
		$types = [
			'product' => Product::class,
			'customer' => Customer::class,
		];

		self::generateOutputs($types, __DIR__ . '/Schema/Outputs', 'EshopApi\\Schema\\Outputs');
		self::generateCreateInputs($types, __DIR__ . '/Schema/Inputs', 'EshopApi\\Schema\\Inputs');
		self::generateUpdateInputs($types, __DIR__ . '/Schema/Inputs', 'EshopApi\\Schema\\Inputs');
		self::generateCrudQueries($types, __DIR__ . '/Schema/Types', 'EshopApi\\Schema\\Types');
		self::generateCrudMutations($types, __DIR__ . '/Schema/Types', 'EshopApi\\Schema\\Types');
		self::generateCrudResolvers($types, __DIR__ . '/Resolvers', 'EshopApi\\Resolvers');
	}
}
