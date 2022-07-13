<?php

namespace App\Base;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Nette\DI\Container;
use Nette\Utils\Reflection;
use StORM\Collection;
use StORM\Meta\Relation;
use StORM\Meta\RelationNxN;

abstract class BaseType extends ObjectType
{
	public const ID_NAME = 'uuid';
	public const MAX_DEPTH = 10;
	public const DEFAULT_SORT = 'this.' . BaseType::ID_NAME;
	public const DEFAULT_ORDER = 'ASC';
	public const DEFAULT_PAGE = 1;
	public const DEFAULT_LIMIT = 50;

	public function __construct(protected Container $container, $config)
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
		if (!isset($config2['fields'])) {
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
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>
	 * @throws \StORM\Exception\GeneralException|\ReflectionException
	 */
	protected function fetchResult(Collection $collection, ResolveInfo $resolveInfo): array
	{
		$fieldSelection = $resolveInfo->getFieldSelection(self::MAX_DEPTH);

		return $this->fetchResultHelper($collection, $fieldSelection);
	}

	/**
	 * @param \StORM\Collection<\StORM\Entity> $collection
	 * @param array<mixed> $fieldSelection
	 * @return array<mixed>
	 * @throws \StORM\Exception\GeneralException
	 * @throws \ReflectionException
	 */
	private function fetchResultHelper(Collection $collection, array $fieldSelection): array
	{
		$objects = [];
		$allRelations = $collection->getRepository()->getStructure()->getRelations();

		$relations = \array_keys(\array_filter(
			$allRelations,
			fn($value, $key): bool => isset($fieldSelection[$key]) && $fieldSelection[$key] && $value::class === Relation::class,
			\ARRAY_FILTER_USE_BOTH,
		));

		$relationCollections = \array_keys(\array_filter(
			$allRelations,
			fn($value, $key): bool => isset($fieldSelection[$key]) && $fieldSelection[$key] && $value::class === RelationNxN::class,
			\ARRAY_FILTER_USE_BOTH,
		));

		while ($object = $collection->fetch()) {
			/** @var \StORM\Entity $object */
			$objects[$object->getPK()] = $object->toArray();
		}

		$keys = \array_keys($objects);

		foreach ($relations as $relationName) {
			if (\is_bool($fieldSelection[$relationName])) {
				continue;
			}

			/** @var class-string<\StORM\Entity> $relationClassType */
			$relationClassType = $allRelations[$relationName]->getTarget();

			$relationObjects = $this->fetchResultHelper(
				$collection->getConnection()->findRepository($relationClassType)
						->many()
						->join(['relation' => $collection->getRepository()->getStructure()->getTable()->getName()], 'this.' . BaseType::ID_NAME . ' = relation.fk_' . $relationName)
						->select(['originalId' => 'relation.' . BaseType::ID_NAME])
						->setIndex('originalId')
						->where('relation.' . BaseType::ID_NAME, $keys),
				$fieldSelection[$relationName],
			);

			foreach ($objects as $object) {
				$objects[$object[BaseType::ID_NAME]][$relationName] = $relationObjects[$object[BaseType::ID_NAME]] ?? null;
			}
		}

		foreach ($relationCollections as $relationName) {
			if (\is_bool($fieldSelection[$relationName])) {
				continue;
			}

			/** @var \StORM\Meta\RelationNxN $relation */
			$relation = $allRelations[$relationName];

			/** @var class-string<\StORM\Entity> $relationClassType */
			$relationClassType = $relation->getTarget();

			$relationObjects = $this->fetchResultHelper(
				$collection->getConnection()->findRepository($relationClassType)
					->many()
					->join(['relationNxN' => $relation->getVia()], 'this.' . BaseType::ID_NAME . ' = relationNxN.' . $relation->getTargetViaKey())
					->select(['originalId' => 'relationNxN.' . $relation->getSourceViaKey()])
					->where('relationNxN.' . $relation->getSourceViaKey(), $keys),
				$fieldSelection[$relationName],
			);

			foreach ($relationObjects as $relationObject) {
				if (isset($objects[$relationObject['originalId']][$relationName])) {
					$objects[$relationObject['originalId']][$relationName][$relationObject[BaseType::ID_NAME]] = $relationObject;
				} else {
					$objects[$relationObject['originalId']][$relationName] = [$relationObject[BaseType::ID_NAME] => $relationObject];
				}
			}
		}

		return $objects;
	}
}
