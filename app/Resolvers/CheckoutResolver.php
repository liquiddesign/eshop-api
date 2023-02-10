<?php

declare(strict_types=1);

namespace EshopApi\Resolvers;

use Eshop\CheckoutManager;
use Eshop\DB\CartRepository;
use GraphQL\Type\Definition\ResolveInfo;
use LqGrAphi\GraphQLContext;
use LqGrAphi\Resolvers\BaseResolver;
use LqGrAphi\Schema\BaseType;
use Nette\DI\Container;
use Nette\Utils\Arrays;
use StORM\DIConnection;
use StORM\SchemaManager;

class CheckoutResolver extends BaseResolver
{
	public function __construct(
		Container $container,
		SchemaManager $schemaManager,
		DIConnection $connection,
		private readonly CheckoutManager $checkoutManager,
		private readonly CartRepository $cartRepository,
	) {
		parent::__construct($container, $schemaManager, $connection);
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>
	 */
	public function getCart(array $rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): array
	{
		unset($rootValue, $args, $context);

		$cart = $this->checkoutManager->cartExists() ? $this->checkoutManager->getCart() : $this->checkoutManager->createCart();

		/* @phpstan-ignore-next-line */
		$results = $this->fetchResult($this->cartRepository->many()->where('this.' . BaseType::ID_NAME, $cart->getPK()), $resolveInfo);

		return Arrays::first($results);
	}
}
