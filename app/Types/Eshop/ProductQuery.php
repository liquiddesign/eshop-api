<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Crud\CrudQuery;
use App\TypeRegister;
use Eshop\DB\Product;

class ProductQuery extends CrudQuery
{
	/**
	 * @inheritDoc
	 */
	public function addCustomFields(string $baseName): array
	{
		/** @var \GraphQL\Type\Definition\Type $outputType */
		$outputType = $this->typeRegister->getOutputType("{$baseName}GetProducts");

		return [
			"{$baseName}GetProducts" => [
				'type' => TypeRegister::nonNull(TypeRegister::listOf($outputType)),
				'args' => [
					'pricelists' => TypeRegister::listOf(TypeRegister::string()),
					'customer' => TypeRegister::string(),
					'selects' => TypeRegister::boolean(),
					'input' => $this->typeRegister->getManyInput(),
				],
			],
		];
	}

	public function getClass(): string
	{
		return Product::class;
	}
}
