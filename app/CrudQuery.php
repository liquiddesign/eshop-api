<?php

namespace App;

use App\Exceptions\NotFoundException;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Nette\DI\Container;
use Nette\Utils\Strings;
use StORM\Entity;
use StORM\Repository;

/**
 * @method array onBeforeGetOne(array $rootValues, array $args)
 * @method array onBeforeGetAll(array $rootValues, array $args)
 */
abstract class CrudQuery extends ObjectType implements IQuery
{
	public const DEFAULT_SORT = 'this.' . IType::ID_NAME;
	public const DEFAULT_ORDER = 'ASC';
	public const DEFAULT_PAGE = 1;
	public const DEFAULT_LIMIT = 50;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetOne = null;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetAll = null;

	abstract public function getName(): string;

	abstract public function getOutputType(): BaseOutput;

	/**
	 * @return class-string
	 */
	abstract public function getRepositoryClass(): string;

	public function __construct(protected readonly Container $container)
	{
		$baseName = Strings::firstUpper($this->getName());
		$outputType = $this->getOutputType();
		$repository = $this->getRepository();

		$config = [
			'fields' => [
				"get$baseName" => [
					'type' => $outputType,
					'args' => [
						IType::ID_NAME => TypeRegistry::nonNull(TypeRegistry::id()),
					],
					'resolve' => function (array $rootValue, array $args) use ($repository): ?Entity {
						if ($this->onBeforeGetOne) {
							[$rootValue, $args] = $this->onBeforeGetOne($rootValue, $args);
						}

						try {
							return $repository->one($args[IType::ID_NAME], true);
						} catch (\Throwable $e) {
							throw new NotFoundException($args[IType::ID_NAME]);
						}
					},
				],
				"get{$baseName}s" => [
					'type' => TypeRegistry::listOf($outputType),
					'args' => [
						'sort' => Type::string(),
						'order' => TypeRegistry::orderEnum(),
						'limit' => Type::int(),
						'page' => Type::int(),
						'filters' => TypeRegistry::JSON(),
					],
					'resolve' => function (array $rootValue, array $args, $a, $b) use ($repository, $outputType): array {
						if ($this->onBeforeGetAll) {
							[$rootValue, $args] = $this->onBeforeGetAll($rootValue, $args);
						}

						$collection = $repository->many()
							->orderBy([$args['sort'] ?? $this::DEFAULT_SORT => $args['order'] ?? $this::DEFAULT_ORDER])
							->setPage($args['page'] ?? $this::DEFAULT_PAGE, $args['limit'] ?? $this::DEFAULT_LIMIT);

						$objects = [];

						while ($object = $collection->fetch()) {
							$objects[$object->getPK()] = $object->toArray();

							foreach ($outputType->getRelations() as $relation) {
								$objects[$object->getPK()][$relation] = $object->$relation->toArray();
							}
						}

						return $objects;
					},
				],
			],
		];

		parent::__construct($config);
	}

	/**
	 * @return \StORM\Repository<\StORM\Entity>
	 */
	protected function getRepository(): Repository
	{
		return $this->container->getByType($this->getRepositoryClass());
	}
}
