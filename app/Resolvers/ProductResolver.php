<?php

namespace App\Resolvers;

use App\Crud\CrudResolver;
use Eshop\DB\Product;
use GraphQL\Type\Definition\ResolveInfo;

class ProductResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Product::class;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param mixed $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>
	 */
	public function getProducts(array $rootValue, array $args, mixed $context, ResolveInfo $resolveInfo): array
	{
		/** @var \Eshop\DB\ProductRepository $repository */
		$repository = $this->getRepository();

		$pricelists = $args['pricelists'] ?? [];
		$customer = $args['customer'] ?? null;
		$selects = $args['selects'] ?? true;

		$products = $repository->getProducts($pricelists, $customer, $selects);

		return $this->fetchResult($products, $resolveInfo);
	}
}
