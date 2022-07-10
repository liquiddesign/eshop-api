<?php

namespace App\Base;

use GraphQL\Type\Definition\ObjectType;
use Nette\DI\Container;
use StORM\Collection;
use StORM\Meta\Relation;
use StORM\Meta\RelationNxN;

abstract class BaseType extends ObjectType
{
	public const ID_NAME = 'uuid';

	public function __construct(array $config, protected Container $container)
	{
		parent::__construct($config);
	}

	/**
	 * @param array<mixed> $config1
	 * @param array<mixed> $config2
	 * @return array<mixed>
	 * @throws \Exception
	 */
	protected function mergeFields(array $config1, array $config2): array
	{
		if (!isset($config1['fields']) || !isset($config2['fields'])) {
			return $config1;
		}

		foreach ($config2['fields'] as $fieldKey => $field) {
			if (isset($config1['fields'][$fieldKey])) {
				throw new \Exception("Field '$fieldKey' already defined.");
			}

			$config1['fields'][$fieldKey] = $field;
		}

		return $config1;
	}

	/**
	 * @param \StORM\Collection<\StORM\Entity> $collection
	 * @param \App\Base\BaseOutput $outputType
	 * @param array<string, bool> $fieldSelection
	 * @return array<mixed>
	 */
	protected function fetchResult(Collection $collection, BaseOutput $outputType, array $fieldSelection): array
	{
		$objects = [];

		$allRelations = $collection->getRepository()->getStructure()->getRelations();

		$relations = \array_keys(\array_filter(
			$allRelations,
			fn ($value, $key): bool => isset($fieldSelection[$key]) && $fieldSelection[$key] === true && $value instanceof Relation,
			\ARRAY_FILTER_USE_BOTH,
		));

		$relationCollections = \array_keys(\array_filter(
			$allRelations,
			fn ($value, $key): bool => isset($fieldSelection[$key]) && $fieldSelection[$key] === true && $value instanceof RelationNxN,
			\ARRAY_FILTER_USE_BOTH,
		));

		while ($object = $collection->fetch()) {
			/** @var \StORM\Entity $object */
			$objects[$object->getPK()] = $object->toArray($relations);

			foreach ($relationCollections as $relation) {
				$objects[$object->getPK()][$relation] = $object->$relation->toArray();
			}
		}

		return $objects;
	}
}
