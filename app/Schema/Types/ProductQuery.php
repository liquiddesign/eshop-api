<?php

declare(strict_types=1);

namespace EshopApi\Schema\Types;

use Eshop\DB\Product;
use LqGrAphi\Schema\CrudQuery;
use LqGrAphi\Schema\TypeRegister;

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
					'pricelists' => [
						'type' => TypeRegister::listOf(TypeRegister::string()),
						'defaultValue' => null,
					],
					'customer' => [
						'type' => TypeRegister::string(),
						'defaultValue' => null,
					],
					'selects' => [
						'type' => TypeRegister::boolean(),
						'defaultValue' => true,
					],
					'manyInput' => $this->typeRegister->getManyInputWithDefaultValue(),
				],
			],
		];
	}

	public function getClass(): string
	{
		return Product::class;
	}
}
