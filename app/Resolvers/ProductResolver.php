<?php

namespace EshopApi\Resolvers;

use Eshop\DB\Product;
use GraphQL\Type\Definition\ResolveInfo;
use LqGrAphi\GraphQLContext;
use LqGrAphi\Resolvers\CrudResolver;

class ProductResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Product::class;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>
	 */
	public function getProducts(array $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
	{
		/** @var \Eshop\DB\ProductRepository $repository */
		$repository = $this->getRepository();

		$pricelists = $args['pricelists'] ?? [];
		$customer = $args['customer'] ?? null;
		$selects = $args['selects'] ?? true;

		$products = $repository->getProducts($pricelists, $customer, $selects);

		$customSelects = [];

		foreach (['price', 'priceVat'] as $item) {
			if (isset($products->getModifiers()['SELECT'][$item])) {
				$customSelects[$item] = $products->getModifiers()['SELECT'][$item];
			}
		}

		return $this->fetchResult($products, $resolveInfo, customSelects: $customSelects);
	}
}
