<?php

namespace App\Schema\Types\Security;

use App\Schema\Base\CrudMutation;
use App\Schema\TypeRegister;
use Eshop\Shopper;
use Nette;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Security\DB\Account;

class AccountMutation extends CrudMutation
{
	public function __construct(Container $container, Passwords $passwords, Shopper $shopper)
	{
		$this->onBeforeCreate = function (array $rootValues, array $args) use ($passwords, $shopper): array {
			$registerConfig = $shopper->getRegistrationConfiguration();

			$args['input']['password'] = $passwords->hash($args['input']['password'] ?? Nette\Utils\Random::generate(8));
			$args['input']['active'] = !$registerConfig['confirmation'];
			$args['input']['authorized'] = !$registerConfig['emailAuthorization'];
			$args['input']['confirmationToken'] = $registerConfig['emailAuthorization'] ? Nette\Utils\Random::generate(128) : null;

			return [$rootValues, $args];
		};

		// Example of added field
		$config = [
			'fields' => [
				'test' => [
					'type' => TypeRegister::nonNull(TypeRegister::string()),
					'args' => ['text' => TypeRegister::nonNull(TypeRegister::string())],
					'resolve' => function (array $rootValue, array $args): string {
						return $args['text'];
					},
				],
			],
		];

		parent::__construct($container, $config);
	}

	public function getClass(): string
	{
		return Account::class;
	}
}