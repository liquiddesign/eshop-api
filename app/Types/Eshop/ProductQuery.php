<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Crud\CrudQuery;
use App\TypeRegister;
use Eshop\DB\Product;
use GraphQL\Type\Definition\Type;

class ProductQuery extends CrudQuery
{
	/**
	 * @inheritDoc
	 */
	public function addCustomFields(string $baseName, Type $outputType): array
	{
		return [
			"{$baseName}GetProducts" => [
				'type' => TypeRegister::nonNull(TypeRegister::listOf($outputType)),
				'args' => [
					'pricelists' => TypeRegister::listOf(TypeRegister::string()),
					'customer' => TypeRegister::string(),
					'selects' => TypeRegister::boolean(),
					'input' => $this->typeRegister->getCollectionInput(),
				],
			],
		];
	}

	public function getClass(): string
	{
		return Product::class;
	}
}
