<?php

namespace App\Resolvers\Base;

use App\Resolvers\Exceptions\BadRequestException;
use App\Schema\Base\BaseType;
use Common\DB\IGeneralRepository;
use GraphQL\Type\Definition\ResolveInfo;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use StORM\Collection;
use StORM\DIConnection;
use StORM\Meta\Relation;
use StORM\Meta\RelationNxN;
use StORM\Repository;
use Tracy\Debugger;

abstract class CrudResolver extends BaseResolver
{
	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetOne = null;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetAll = null;

	/**
	 * @var \StORM\Repository<\StORM\Entity>
	 */
	private Repository $repository;

	/**
	 * @return class-string<\StORM\Entity>
	 */
	abstract public function getClass(): string;

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param mixed $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>|null
	 * @throws \App\Resolvers\Exceptions\NotFoundException
	 * @throws \ReflectionException
	 * @throws \StORM\Exception\GeneralException
	 */
	public function one(array $rootValue, array $args, mixed $context, ResolveInfo $resolveInfo): ?array
	{
		if ($this->onBeforeGetOne) {
			[$rootValue, $args] = \call_user_func($this->onBeforeGetOne, $rootValue, $args);
		}

		$results = $this->fetchResult($this->getRepository()->many()->where('this.' . BaseType::ID_NAME, $args[BaseType::ID_NAME]), $resolveInfo);

		return $results ? Arrays::first($results) : null;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param mixed $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>|null
	 * @throws \App\Resolvers\Exceptions\BadRequestException
	 * @throws \ReflectionException
	 * @throws \StORM\Exception\GeneralException
	 */
	public function many(array $rootValue, array $args, mixed $context, ResolveInfo $resolveInfo): ?array
	{
		if ($this->onBeforeGetAll) {
			[$rootValue, $args] = \call_user_func($this->onBeforeGetAll, $rootValue, $args);
		}

		$collection = $this->getRepository()->many()
			->orderBy([$args['sort'] ?? BaseType::DEFAULT_SORT => $args['order'] ?? BaseType::DEFAULT_ORDER])
			->setPage($args['page'] ?? BaseType::DEFAULT_PAGE, $args['limit'] ?? BaseType::DEFAULT_LIMIT);

		try {
			$collection->filter((array) ($args['filters'] ?? []));
		} catch (\Throwable $e) {
			throw new BadRequestException('Invalid filters');
		}

		Debugger::log('frb:' . Debugger::timer());

		$result = $this->fetchResult($collection, $resolveInfo);

		Debugger::log('fra:' . Debugger::timer());

		return $result;
	}

	/**
	 * @param array<mixed> $rootValue
	 * @param array<mixed> $args
	 * @param mixed $context
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>|null
	 * @throws \App\Resolvers\Exceptions\BadRequestException
	 * @throws \ReflectionException
	 * @throws \StORM\Exception\GeneralException
	 */
	public function collection(array $rootValue, array $args, mixed $context, ResolveInfo $resolveInfo): ?array
	{
		if ($this->onBeforeGetAll) {
			[$rootValue, $args] = \call_user_func($this->onBeforeGetAll, $rootValue, $args);
		}

		$repository = $this->getRepository();

		\assert($repository instanceof IGeneralRepository);

		$collection = $repository->getCollection()
			->orderBy([$args['sort'] ?? BaseType::DEFAULT_SORT => $args['order'] ?? BaseType::DEFAULT_ORDER])
			->setPage($args['page'] ?? BaseType::DEFAULT_PAGE, $args['limit'] ?? BaseType::DEFAULT_LIMIT);

		try {
			$collection->filter((array) ($args['filters'] ?? []));
		} catch (\Throwable $e) {
			throw new BadRequestException('Invalid filters');
		}

		Debugger::log('frb:' . Debugger::timer());

		$result = $this->fetchResult($collection, $resolveInfo);

		\var_dump($result);
		die();

		Debugger::log('fra:' . Debugger::timer());

		return $result;
	}

	public function getName(): string
	{
		$reflection = new \ReflectionClass($this->getClass());

		return Strings::lower($reflection->getShortName());
	}

	/**
	 * @return \StORM\Repository<\StORM\Entity>
	 */
	protected function getRepository(): Repository
	{
		return $this->repository ??= $this->container->getByType(DIConnection::class)->findRepository($this->getClass());
	}

	/**
	 * @param \StORM\Collection<\StORM\Entity> $collection
	 * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
	 * @return array<mixed>
	 * @throws \StORM\Exception\GeneralException|\ReflectionException
	 */
	protected function fetchResult(Collection $collection, ResolveInfo $resolveInfo): array
	{
		$fieldSelection = $resolveInfo->getFieldSelection(BaseType::MAX_DEPTH);

		$result = $this->fetchResultHelper($collection, $fieldSelection);

		$result['onPageCount'] = \count($result);
		$result['totalCount'] = $collection->clear()->enum();

		return $result;
	}

	/**
	 * @param \StORM\Collection<\StORM\Entity> $collection
	 * @param array<mixed> $fieldSelection
	 * @param string|null $selectOriginalId
	 * @return array<mixed>
	 * @throws \ReflectionException
	 * @throws \StORM\Exception\GeneralException
	 */
	private function fetchResultHelper(Collection $collection, array $fieldSelection, ?string $selectOriginalId = null,): array
	{
		$objects = [];
		$structure = $collection->getRepository()->getStructure();
		$allRelations = $structure->getRelations();
		$mutationSuffix = $collection->getConnection()->getMutationSuffix();

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

		$ormFieldSelection = [BaseType::ID_NAME => 'this.uuid'];

		foreach (\array_keys($fieldSelection) as $select) {
			if (Arrays::contains($relations, $select)) {
				$ormFieldSelection[$select] = "this.fk_$select";

				continue;
			}

			if (Arrays::contains($relationCollections, $select)) {
				continue;
			}

			if (!$column = $structure->getColumn($select)) {
				continue;
			}

			if ($column->hasMutations()) {
				$ormFieldSelection[$select] = "this.$select$mutationSuffix";

				continue;
			}

			$ormFieldSelection[$select] = "this.$select";
		}

		$collection->setSelect(($selectOriginalId ? ['originalId' => $selectOriginalId] : []) + $ormFieldSelection);

		foreach ($collection->fetchArray(\stdClass::class) as $object) {
			$objects[$object->{BaseType::ID_NAME}] = \get_object_vars($object);
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
					->setIndex('originalId')
					->where('relation.' . BaseType::ID_NAME, $keys),
				$fieldSelection[$relationName],
				'relation.' . BaseType::ID_NAME,
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

			$relationClassType = $relation->getTarget();

			$relationObjects = $this->fetchResultHelper(
				$collection->getConnection()->findRepository($relationClassType)
					->many()
					->join(['relationNxN' => $relation->getVia()], 'this.' . BaseType::ID_NAME . ' = relationNxN.' . $relation->getTargetViaKey())
					->where('relationNxN.' . $relation->getSourceViaKey(), $keys),
				$fieldSelection[$relationName],
				'relationNxN.' . $relation->getSourceViaKey(),
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