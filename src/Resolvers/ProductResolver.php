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
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 * @return array<mixed>
	 */
	public function getProducts(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): array
	{
		unset($rootValue, $context);

		/** @var \Eshop\DB\ProductRepository $repository */
		$repository = $this->getRepository();

		/** @var \StORM\Collection<\StORM\Entity> $products */
		$products = $repository->getProducts($args['pricelists'], $args['customer'], $args['selects']);

		return $this->fetchResult($products, $resolveInfo, $args['manyInput']);
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 */
	public function getProductsTotalCount(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): int
	{
		return \count($this->getProducts($rootValue, $args, $context, $resolveInfo));
	}
}
