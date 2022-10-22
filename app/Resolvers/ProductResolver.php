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

		$products = $repository->getProducts($args['pricelists'], $args['customer'], $args['selects']);

		return $this->fetchResult($products, $resolveInfo, $args['manyInput']);
	}
}
