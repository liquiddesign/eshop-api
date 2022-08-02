<?php

namespace App;

use App\Base\BaseInput;
use App\Base\BaseOutput;
use App\Base\BaseType;
use App\TypeRegistries\AdminTypeRegister;
use App\TypeRegistries\EshopTypeRegister;
use App\TypeRegistries\SecurityTypeRegister;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\Type;
use MLL\GraphQLScalars\Date;
use MLL\GraphQLScalars\DateTime;
use MLL\GraphQLScalars\JSON;
use MLL\GraphQLScalars\MixedScalar;
use MLL\GraphQLScalars\NullScalar;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use StORM\RelationCollection;
use StORM\SchemaManager;

class TypeRegister extends Type
{
	use AdminTypeRegister;
	use EshopTypeRegister;
	use SecurityTypeRegister;
	/**
	 * @var array<string, mixed>
	 */
	public array $types = [];

	/**
	 * @var array<string, mixed>
	 */
	public array $inputTypes = [];

	public function __construct(private readonly SchemaManager $schemaManager)
	{
	}

	public function orderEnum(): OrderEnum
	{
		return $this->types['order'] ??= new OrderEnum();
	}

	public function JSON(): JSON
	{
		return $this->types['JSON'] ??= new JSON();
	}

	public function datetime(): DateTime
	{
		return $this->types['datetime'] ??= new DateTime();
	}

	public function date(): Date
	{
		return $this->types['date'] ??= new Date();
	}

	public function null(): NullScalar
	{
		return $this->types['null'] ??= new NullScalar();
	}

	public function mixed(): MixedScalar
	{
		return $this->types['mixed'] ??= new MixedScalar();
	}

	/**
	 * @param class-string<\StORM\Entity> $class
	 * @param array<string>|null $include
	 * @param array<string> $exclude
	 * @param array<string> $forceRequired
	 * @param array<string> $forceOptional
	 * @param bool $forceAllOptional
	 * @return array<mixed>
	 * @throws \ReflectionException
	 */
	public function createOutputFieldsFromClass(
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
							$relation = $this->schemaManager->getStructure($class)->getRelation($property->getName());

							if (!$relation) {
								throw new \Exception('Fatal error! Unknown relation "' . $property->getName() . '".');
							}

							$typeName = $relation->getTarget();
							$array = true;
						}
					}

					$type = $this->get(Strings::lower(Strings::substring($typeName, \strrpos($typeName, '\\') + 1)));
				}

				$isForceRequired = Arrays::contains($forceRequired, $name);
				$isForceOptional = Arrays::contains($forceOptional, $name);

				if ($isForceRequired && $isForceOptional) {
					throw new \Exception("Property '$name' can't be forced optional and required at same time.");
				}

				if (($array || ($forceAllOptional === false && ((!$forceOptional && $forceRequired) || (!$forceOptional && !$reflectionType->allowsNull())))) && $type instanceof NullableType) {
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

	/**
	 * @param class-string<\StORM\Entity> $class
	 * @param array<string>|null $include
	 * @param array<string> $exclude
	 * @param array<string> $forceRequired
	 * @param array<string> $forceOptional
	 * @param bool $forceAllOptional
	 * @return array<mixed>
	 * @throws \ReflectionException
	 */
	public function createInputFieldsFromClass(
		string $class,
		?array $include = null,
		array $exclude = [],
		array $forceRequired = [],
		array $forceOptional = [],
		bool $forceAllOptional = false,
		bool $includeId = true,
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
							$relation = $this->schemaManager->getStructure($class)->getRelation($property->getName());

							if (!$relation) {
								throw new \Exception('Fatal error! Unknown relation "' . $property->getName() . '".');
							}

							$typeName = $relation->getTarget();
							$array = true;
						}
					}

					$type = $this->get(Strings::lower(Strings::substring($typeName, \strrpos($typeName, '\\') + 1)), TypeEnum::INPUT);
				}

				$isForceRequired = Arrays::contains($forceRequired, $name);
				$isForceOptional = Arrays::contains($forceOptional, $name);

				if ($isForceRequired && $isForceOptional) {
					throw new \Exception("Property '$name' can't be forced optional and required at same time.");
				}

				if (($array || ($forceAllOptional === false && ((!$forceOptional && $forceRequired) || (!$forceOptional && !$reflectionType->allowsNull())))) && $type instanceof NullableType) {
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

	public function get(string $name, TypeEnum $typeEnum = TypeEnum::OUTPUT): Type
	{
		if ($typeEnum === TypeEnum::INPUT) {
			$name .= 'UpdateInput';
		}

		if (!\method_exists($this::class, $name)) {
			$found = false;

			if ($traits = \class_uses($this::class)) {
				foreach ($traits as $traitClass) {
					if (\method_exists($traitClass, $name)) {
						$found = true;

						break;
					}
				}
			}

			if (!$found) {
				return static::mixed();
			}
		}

		$type = $this->types[$name] ??= static::{$name}();

		if ($typeEnum === TypeEnum::INPUT && !$type instanceof BaseInput) {
			throw new \Exception("Type '$name' is not input type!");
		}

		if ($typeEnum === TypeEnum::OUTPUT && !$type instanceof BaseOutput) {
			throw new \Exception("Type '$name' is not output type!");
		}

		return $type;
	}
}
