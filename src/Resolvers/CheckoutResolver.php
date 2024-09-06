<?php

declare(strict_types=1);

namespace EshopApi\Resolvers;

use Eshop\BuyException;
use Eshop\DB\CartItemRepository;
use Eshop\DB\CartRepository;
use Eshop\DB\ProductRepository;
use Eshop\ShopperUser;
use GraphQL\Type\Definition\ResolveInfo;
use LqGrAphi\GraphQLContext;
use LqGrAphi\Resolvers\BaseResolver;
use LqGrAphi\Resolvers\Exceptions\NotFoundException;
use LqGrAphi\Schema\BaseType;
use Nette\Application\BadRequestException;
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
		private readonly ShopperUser $shopperUser,
		private readonly CartRepository $cartRepository,
		private readonly ProductRepository $productRepository,
		private readonly CartItemRepository $cartItemRepository
	) {
		parent::__construct($container, $schemaManager, $connection);
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 * @return array<mixed>
	 */
	public function getCart(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): array
	{
		unset($rootValue, $args, $context);

		$cart = $this->shopperUser->getCheckoutManager()->cartExists() ? $this->shopperUser->getCheckoutManager()->getCart() : $this->shopperUser->getCheckoutManager()->createCart();

		$results = $this->fetchResult($this->cartRepository->many()->where('this.' . BaseType::ID_NAME, $cart->getPK()), $resolveInfo);

		return Arrays::first($results);
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 * @return array<mixed>
	 * @throws \Nette\Application\BadRequestException
	 * @throws \LqGrAphi\Resolvers\Exceptions\NotFoundException
	 */
	public function addItemToCart(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): array
	{
		unset($rootValue, $context);

		$input = $args['input'];

		if ($input['amount'] < 1) {
			throw new \LqGrAphi\Resolvers\Exceptions\BadRequestException('Amount must be greater then 1');
		}

		try {
			/** @var \Eshop\DB\Product $product */
			$product = $this->productRepository->getProducts()->where('this.hidden', false)->where('this.uuid', $input['product'])->first(true);
		} catch (\StORM\Exception\NotFoundException $e) {
			throw new NotFoundException($input['product'], 'Product');
		}

		try {
			$cartItem = $this->shopperUser->getCheckoutManager()->addItemToCart(
				product: $product,
				variant: $input['variant'],
				amount: $input['amount'],
				replaceMode: $input['replaceMode'],
				checkInvalidAmount: $input['checkInvalidAmount'],
				checkCanBuy: $input['checkCanBuy'],
				cart: $input['cart'],
				upsell: $input['upsell'],
			);
		} catch (BuyException | \StORM\Exception\NotFoundException $e) {
			throw new BadRequestException($e->getMessage());
		}

		return Arrays::first($this->fetchResult($this->cartItemRepository->many()->where('this.uuid', $cartItem->getPK()), $resolveInfo));
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 */
	public function deleteItemFromCart(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): int
	{
		unset($rootValue, $context, $resolveInfo);

		try {
			$cartItem = $this->cartItemRepository->one($args['item'], true);
		} catch (\StORM\Exception\NotFoundException $e) {
			throw new NotFoundException($args['item']);
		}

		return $this->cartItemRepository->deleteItem($this->shopperUser->getCheckoutManager()->getCart(), $cartItem);
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 */
	public function changeAmountOfCartItem(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): int
	{
		unset($rootValue, $context, $resolveInfo);

		if ($args['amount'] < 1) {
			throw new \LqGrAphi\Resolvers\Exceptions\BadRequestException('Amount must be greater then 1');
		}

		try {
			$cartItem = $this->cartItemRepository->one($args['item'], true);
		} catch (\StORM\Exception\NotFoundException $e) {
			throw new NotFoundException($args['item']);
		}

		$product = $cartItem->product;

		if (!$product) {
			throw new \LqGrAphi\Resolvers\Exceptions\BadRequestException('CartItem has no assigned Product');
		}

		$this->shopperUser->getCheckoutManager()->changeCartItemAmount($product, $cartItem, $args['amount'], $args['checkInvalidAmount']);

		return 1;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 */
	public function deleteCart(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): int
	{
		unset($rootValue, $args, $context, $resolveInfo);

		if ($this->shopperUser->getCheckoutManager()->cartExists()) {
			$this->shopperUser->getCheckoutManager()->deleteCart();

			return 1;
		}

		return 0;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param \LqGrAphi\GraphQLContext $context
	 * @param \GraphQL\Type\Definition\ResolveInfo|array<mixed> $resolveInfo)
	 * @return array<mixed>
	 */
	public function getCartItems(array $rootValue, array $args, GraphQLContext $context, ResolveInfo|array $resolveInfo): array
	{
		unset($rootValue, $args, $context);

		return $this->fetchResult($this->shopperUser->getCheckoutManager()->getItems(), $resolveInfo);
	}
}
