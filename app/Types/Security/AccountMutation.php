<?php

namespace App\Types\Security;

use App\Base\BaseInput;
use App\Base\BaseOutput;
use App\Crud\CrudMutation;
use App\TypeRegister;
use Eshop\Shopper;
use Nette;
use Nette\DI\Container;
use Nette\Security\Passwords;
use Security\DB\AccountRepository;

class AccountMutation extends CrudMutation
{
	public function __construct(Container $container, Passwords $passwords, Shopper $shopper, private readonly TypeRegister $typeRegister)
	{
		$this->onBeforeCreate = function (array $rootValues, array $args) use ($passwords, $shopper): array {
			$registerConfig = $shopper->getRegistrationConfiguration();

			$args['input']['password'] = $passwords->hash($args['input']['password'] ?? Nette\Utils\Random::generate(8));
			$args['input']['active'] = !$registerConfig['confirmation'];
			$args['input']['authorized'] = !$registerConfig['emailAuthorization'];
			$args['input']['confirmationToken'] = $registerConfig['emailAuthorization'] ? Nette\Utils\Random::generate(128) : null;

			return [$rootValues, $args];
		};

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

	public function getName(): string
	{
		return 'account';
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->account();
	}

	public function getCreateInputType(): BaseInput
	{
		return $this->typeRegister->accountCreateInput();
	}

	public function getUpdateInputType(): BaseInput
	{
		return $this->typeRegister->accountUpdateInput();
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
