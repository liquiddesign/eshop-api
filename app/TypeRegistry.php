<?php

namespace App;

use Admin\DB\Role;
use App\Base\BaseType;
use App\Inputs\AccountCreateInput;
use App\Inputs\AccountUpdateInput;
use App\Outputs\AccountOutput;
use App\Outputs\AdministratorOutput;
use App\Outputs\RoleOutput;
use GraphQL\Type\Definition\Type;
use MLL\GraphQLScalars\Date;
use MLL\GraphQLScalars\DateTime;
use MLL\GraphQLScalars\JSON;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use Security\DB\Account;
use StORM\RelationCollection;

class TypeRegistry extends Type
{
	/**
	 * @var array<string>
	 */
	public static array $outputTypesMap = [
		Account::class => 'account',
		Role::class => 'role',
	];

	/**
	 * @var array<string, mixed>
	 */
	public static array $types = [];

	public static function orderEnum(): OrderEnum
	{
		return static::$types['order'] ??= new OrderEnum();
	}

	public static function JSON(): JSON
	{
		return static::$types['JSON'] ??= new JSON();
	}

	public static function datetime(): DateTime
	{
		return static::$types['datetime'] ??= new DateTime();
	}

	public static function date(): Date
	{
		return static::$types['date'] ??= new Date();
	}

	public static function account(): AccountOutput
	{
		return static::$types['account'] ??= new AccountOutput();
	}

	public static function accountCreate(): AccountCreateInput
	{
		return static::$types['accountCreate'] ??= new AccountCreateInput();
	}

	public static function accountUpdate(): AccountUpdateInput
	{
		return static::$types['accountUpdate'] ??= new AccountUpdateInput();
	}

	public static function administrator(): AdministratorOutput
	{
		return static::$types['administrator'] ??= new AdministratorOutput();
	}

	public static function role(): RoleOutput
	{
		return static::$types['role'] ??= new RoleOutput();
	}

	/**
	 * @param class-string $class
	 * @param array<string>|null $include
	 * @param array<string> $exclude
	 * @param array<string> $forceRequired
	 * @param array<string> $forceOptional
	 * @param bool $forceAllOptional
	 * @return array<mixed>
	 * @throws \ReflectionException
	 */
	public static function createFieldsFromClass(
		string $class,
		?array $include = null,
		array $exclude = [],
		array $forceRequired = [],
		array $forceOptional = [],
		bool $forceAllOptional = false,
		bool $includeId = true
	): array {
		$reflection = new \ReflectionClass($class);

		$fields = $includeId ? [
			BaseType::ID_NAME => static::nonNull(static::id()),
		] : [];

		foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
			$name = $property->getName();

			if ($include) {
				if (!Arrays::contains($include, $name) || Arrays::contains($exclude, $name)) {
					continue;
				}
			} else {
				if (Arrays::contains($exclude, $name)) {
					continue;
				}
			}

			/** @var \ReflectionNamedType|null $reflectionType */
			$reflectionType = $property->getType();

			if (!$reflectionType) {
				continue;
			}

			$typeName = $reflectionType->getName();

			$fields[$name] = function () use ($typeName, $property, $forceOptional, $forceRequired, $name, $forceAllOptional, $reflectionType, $class) {
				$array = false;
				$type = match ($typeName) {
					'int' => static::int(),
					'float' => static::float(),
					'bool' => static::boolean(),
					'string' => static::string(),
					default => null,
				};

				$doc = $property->getDocComment();

				if ($doc) {
					if (Strings::contains($doc, '"type":"datetime"') || Strings::contains($doc, '"type":"timestamp"')) {
						$type = static::datetime();
					}

					if (Strings::contains($doc, '"type":"date"')) {
						$type = static::date();
					}
				}

				if ($type === null) {
					if ($doc) {
						if ($typeName === RelationCollection::class && Strings::contains($doc, $typeName)) {
							$start = \strpos($doc, $typeName);

							if ($start === false) {
								throw new \Exception("Error while processing type of '$class:$name'.");
							}

							$start = \strpos($doc, '<', $start) + 1;

							$end = \strpos($doc, '>', $start);

							if ($end === false) {
								throw new \Exception("Error while processing type of '$class:$name'.");
							}

							$typeName = Strings::substring($doc, $start + 1, $end - $start - 1);
							$array = true;
						}
					}

					if (!isset(static::$outputTypesMap[$typeName])) {
						throw new \Exception("Unknown GraphQL type '$typeName'. Did you configured mapping correctly?");
					}

					/** @var \GraphQL\Type\Definition\ObjectType $type */
					$type = static::get(static::$outputTypesMap[$typeName]);
				}

				$isForceRequired = Arrays::contains($forceRequired, $name);
				$isForceOptional = Arrays::contains($forceOptional, $name);

				if ($isForceRequired && $isForceOptional) {
					throw new \Exception("Property '$name' can't be forced optional and required at same time.");
				}

				if ($forceAllOptional === false && ((!$forceOptional && $forceRequired) || (!$forceOptional && !$reflectionType->allowsNull()))) {
					$type = static::nonNull($type);
				}

				if ($array) {
					$type = static::listOf($type);
				}

				return $type;
			};
		}

		return $fields;
	}

	public static function get(string $name): Type
	{
		return static::$types[$name] ??= static::{$name}();
	}
}
