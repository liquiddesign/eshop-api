<?php

namespace App\Base;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use Nette\DI\Container;
use Nette\Utils\Arrays;
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
	private function fetchResultHelper(Collection $collection, array $fieldSelection, ?string $selectOriginalId = null,): array
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

		$ormFieldSelection = [$this::ID_NAME => 'this.uuid'];

		foreach (\array_keys($fieldSelection) as $select) {
			if (Arrays::contains($relations, $select)) {
				$ormFieldSelection[$select] = "this.fk_$select";

				continue;
			}

			if (Arrays::contains($relationCollections, $select)) {
				continue;
			}

			$ormFieldSelection[$select] = "this.$select";
		}

		$collection->setSelect(($selectOriginalId ? ['originalId' => $selectOriginalId] : []) + $ormFieldSelection);

		foreach ($collection->fetchArray(\stdClass::class) as $object) {
			$objects[$object->{$this::ID_NAME}] = \get_object_vars($object);
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
						->join(['relation' => $collection->getRepository()->getStructure()->getTable()->getName()], 'this.' . $this::ID_NAME . ' = relation.fk_' . $relationName)
						->setIndex('originalId')
						->where('relation.' . $this::ID_NAME, $keys),
				$fieldSelection[$relationName],
				'relation.' . $this::ID_NAME,
			);

			foreach ($objects as $object) {
				$objects[$object[$this::ID_NAME]][$relationName] = $relationObjects[$object[$this::ID_NAME]] ?? null;
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
					->join(['relationNxN' => $relation->getVia()], 'this.' . $this::ID_NAME . ' = relationNxN.' . $relation->getTargetViaKey())
					->where('relationNxN.' . $relation->getSourceViaKey(), $keys),
				$fieldSelection[$relationName],
				'relationNxN.' . $relation->getSourceViaKey(),
			);

			foreach ($relationObjects as $relationObject) {
				if (isset($objects[$relationObject['originalId']][$relationName])) {
					$objects[$relationObject['originalId']][$relationName][$relationObject[$this::ID_NAME]] = $relationObject;
				} else {
					$objects[$relationObject['originalId']][$relationName] = [$relationObject[$this::ID_NAME] => $relationObject];
				}
			}
		}

		return $objects;
	}
}
