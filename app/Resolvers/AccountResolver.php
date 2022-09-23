<?php

namespace App\Resolvers;

use App\Resolvers\Base\CrudResolver;
use Eshop\Shopper;
use GraphQL\Type\Definition\ResolveInfo;
use Nette;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Security\DB\Account;

class AccountResolver extends CrudResolver
{
	public function __construct(Container $container)
	{
		$passwords = $container->getByType(Passwords::class);
		$shopper = $container->getByType(Shopper::class);

		$this->onBeforeCreate = function (array $rootValues, array $args) use ($passwords, $shopper): array {
			$registerConfig = $shopper->getRegistrationConfiguration();

			$args['input']['password'] = $passwords->hash($args['input']['password'] ?? Nette\Utils\Random::generate(8));
			$args['input']['active'] = !$registerConfig['confirmation'];
			$args['input']['authorized'] = !$registerConfig['emailAuthorization'];
			$args['input']['confirmationToken'] = $registerConfig['emailAuthorization'] ? Nette\Utils\Random::generate(128) : null;

			return [$rootValues, $args];
		};

		parent::__construct($container);
	}

	public function getClass(): string
	{
		return Account::class;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param mixed $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 */
	public function test(array $rootValue, array $args, mixed $context, ResolveInfo $resolveInfo): string
	{
		return $args['text'];
	}
}
