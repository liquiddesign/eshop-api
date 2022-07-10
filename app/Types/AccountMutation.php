<?php

namespace App\Types;

use App\BaseInput;
use App\BaseOutput;
use App\CrudMutation;
use App\Exceptions\NotFoundException;
use App\IMutation;
use App\IType;
use App\Outputs\AccountOutput;
use App\TypeRegistry;
use Eshop\Shopper;
use Nette;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Security\DB\AccountRepository;

class AccountMutation extends CrudMutation
{
	public function __construct(Container $container, Passwords $passwords, Shopper $shopper,)
	{
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

	public function getName(): string
	{
		return 'account';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegistry::account();
	}

	public function getCreateInputType(): BaseInput
	{
		return TypeRegistry::accountCreate();
	}

	public function getUpdateInputType(): BaseInput
	{
		return TypeRegistry::accountUpdate();
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
