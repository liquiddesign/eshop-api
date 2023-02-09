<?php

namespace EshopApi\Schema\Types;

use Eshop\DB\Cart;
use GraphQL\Type\Definition\Type;
use LqGrAphi\Schema\BaseQuery;
use LqGrAphi\Schema\TypeRegister;
use Nette\DI\Container;

class CheckoutQuery extends BaseQuery
{
	public function __construct(protected Container $container)
	{
		$typeRegister = $this->container->getByType(TypeRegister::class);

		$config = [
			'fields' => [
				'checkoutGetCart' => [
					'type' => Type::nonNull($typeRegister->getOutputType(Cart::class)),
				],
			],
		];

		parent::__construct($container, $config);
	}
}
