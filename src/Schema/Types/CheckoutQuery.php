<?php

namespace EshopApi\Schema\Types;

use Eshop\DB\Cart;
use Eshop\DB\CartItem;
use GraphQL\Type\Definition\Type;
use LqGrAphi\Schema\BaseQuery;
use LqGrAphi\Schema\TypeRegister;
use Nette\DI\Container;

class CheckoutQuery extends BaseQuery
{
	public function __construct(protected Container $container)
	{
		$typeRegister = $this->container->getByType(TypeRegister::class);

		$cartOutput = TypeRegister::getNullableType($typeRegister->getOutputType(Cart::class));
		$cartItemOutput = TypeRegister::getNullableType($typeRegister->getOutputType(CartItem::class));

		$config = [
			'fields' => [
				'checkoutGetCart' => [
					'type' => Type::nonNull($cartOutput),
				],
				'checkoutGetCartItems' => [
					'type' => Type::nonNull(Type::listOf($cartItemOutput)),
				],
			],
		];

		parent::__construct($container, $config);
	}
}
