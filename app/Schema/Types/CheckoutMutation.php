<?php

namespace EshopApi\Schema\Types;

use Eshop\DB\CartItem;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use LqGrAphi\Schema\BaseMutation;
use LqGrAphi\Schema\TypeRegister;
use Nette\DI\Container;

class CheckoutMutation extends BaseMutation
{
	public function __construct(Container $container)
	{
		/** @var \LqGrAphi\Schema\TypeRegister $typeRegister */
		$typeRegister = $container->getByType(TypeRegister::class);

		$cartItemOutput = TypeRegister::getNullableType($typeRegister->getOutputType(CartItem::class));

		$config = [
			'fields' => [
				'checkoutAddItemToCart' => [
					'args' => [
						'input' => Type::nonNull(new InputObjectType([
							'name' => 'CheckoutAddItemToCartInput',
							'fields' => [
								'product' => Type::nonNull(Type::string()),
								'amount' => [
									'type' => Type::int(),
									'defaultValue' => 1,
								],
								'variant' => [
									'type' => Type::string(),
									'defaultValue' => null,
								],
								'replaceMode' => [
									'type' => Type::boolean(),
									'defaultValue' => false,
									'description' => 'true - replace | false - add or update | null - only add',
								],
								'checkInvalidAmount' => [
									'type' => Type::boolean(),
									'defaultValue' => true,
								],
								'checkCanBuy' => [
									'type' => Type::boolean(),
									'defaultValue' => true,
								],
								'cart' => [
									'type' => Type::string(),
									'defaultValue' => null,
								],
								'upsell' => [
									'type' => Type::string(),
									'defaultValue' => null,
									'description' => 'CartItem',
								],
							],
						])),
					],
					'type' => Type::nonNull($cartItemOutput),
				],
				'checkoutChangeAmountOfCartItem' => [
					'args' => [
						'item' => Type::nonNull(Type::string()),
						'amount' => Type::nonNull(Type::int()),
						'checkInvalidAmount' => [
							'type' => Type::boolean(),
							'defaultValue' => true,
						],
					],
					'type' => Type::int(),
				],
				'checkoutDeleteItemFromCart' => [
					'args' => [
						'item' => Type::nonNull(Type::string()),
					],
					'type' => Type::int(),
				],
				'checkoutDeleteCart' => [
					'type' => Type::int(),
				],
			],
		];

		parent::__construct($container, $config);
	}
}
