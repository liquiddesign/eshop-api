<?php

declare(strict_types=1);

namespace EshopApi\Schema\Types;

use Eshop\DB\Product;
use GraphQL\Type\Definition\Type;
use LqGrAphi\Schema\CrudQuery;

class ProductQuery extends CrudQuery
{
	/**
	 * @inheritDoc
	 */
	public function addCustomFields(string $baseName): array
	{
		$outputType = $this->typeRegister->getOutputType("{$baseName}GetProducts");

		return [
			"{$baseName}GetProducts" => [
				'type' => Type::nonNull(Type::listOf($outputType)),
				'args' => [
					'pricelists' => [
						'type' => Type::listOf(Type::string()),
						'defaultValue' => null,
					],
					'customer' => [
						'type' => Type::string(),
						'defaultValue' => null,
					],
					'selects' => [
						'type' => Type::boolean(),
						'defaultValue' => true,
					],
					'manyInput' => $this->typeRegister->getManyInputWithDefaultValue(),
				],
			],
			"{$baseName}GetProductsTotalCount" => [
				'type' => Type::nonNull(Type::int()),
				'args' => [
					'pricelists' => [
						'type' => Type::listOf(Type::string()),
						'defaultValue' => null,
					],
					'customer' => [
						'type' => Type::string(),
						'defaultValue' => null,
					],
					'selects' => [
						'type' => Type::boolean(),
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
