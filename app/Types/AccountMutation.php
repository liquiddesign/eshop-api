<?php

namespace App\Types;

use App\Exceptions\NotFoundException;
use App\IMutation;
use App\IType;
use App\TypeRegistry;
use Eshop\Shopper;
use Nette;
use Nette\Security\Passwords;
use Security\DB\AccountRepository;

class AccountMutation extends \GraphQL\Type\Definition\ObjectType implements IMutation
{
	public function __construct(AccountRepository $accountRepository, Passwords $passwords, Shopper $shopper,)
	{
		$config = [
			'fields' => [
				'createAccount' => [
					'type' => TypeRegistry::account(),
					'args' => [
						'input' => TypeRegistry::accountCreate(),
					],
					'resolve' => function (array $rootValue, array $args) use ($accountRepository, $shopper, $passwords): \Security\DB\Account {
						$registerConfig = $shopper->getRegistrationConfiguration();

						$values = $args['input'];

						$values['password'] = $passwords->hash($values['password'] ?? Nette\Utils\Random::generate(8));
						$values['active'] = !$registerConfig['confirmation'];
						$values['authorized'] = !$registerConfig['emailAuthorization'];
						$values['confirmationToken'] = $registerConfig['emailAuthorization'] ? Nette\Utils\Random::generate(128) : null;

						/** @var \Security\DB\Account $account */
						$account = $accountRepository->createOne($values);

						return $account;
					},
				],
				'updateAccount' => [
					'type' => TypeRegistry::account(),
					'args' => [
						'input' => TypeRegistry::accountUpdate(),
					],
					'resolve' => function (array $rootValue, array $args) use ($accountRepository): \Security\DB\Account {
						$affected = $accountRepository->many()->where('this.' . IType::ID_NAME, $args['input'][IType::ID_NAME])->update($args['input']);

						if ($affected === 0) {
							throw new NotFoundException($args['input'][IType::ID_NAME]);
						}

						return $accountRepository->one($args['input'][IType::ID_NAME], true);
					},
				],
				'deleteAccount' => [
					'type' => TypeRegistry::int(),
					'args' => [
						IType::ID_NAME => TypeRegistry::id(),
					],
					'resolve' => function (array $rootValue, array $args) use ($accountRepository): int {
						return ($account = $accountRepository->one($args[IType::ID_NAME])) ? $account->delete() : 0;
					},
				],
			],
		];

		parent::__construct($config);
	}
}
