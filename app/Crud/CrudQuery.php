<?php

namespace App\Crud;

use App\Base\BaseQuery;
use App\Base\BaseType;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\TypeRegister;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Nette\DI\Container;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use StORM\Repository;

/**
 * @method array onBeforeGetOne(array $rootValues, array $args)
 * @method array onBeforeGetAll(array $rootValues, array $args)
 */
abstract class CrudQuery extends BaseQuery
{
	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetOne = null;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeGetAll = null;

	private TypeRegister $typeRegister;

	abstract public function getName(): string;

	/**
	 * @return class-string
	 */
	abstract public function getRepositoryClass(): string;

	public function __construct(protected Container $container, array $config = [])
	{
		/** @var \App\TypeRegister $typeRegister */
		$typeRegister = $this->container->getByType(TypeRegister::class);
		$this->typeRegister = $typeRegister;

		$baseName = Strings::firstUpper($this->getName());
		$outputType = $this->getOutputType();
		$repository = $this->getRepository();

		\assert($outputType instanceof NullableType);
		\assert($outputType instanceof Type);

		$config = $this->mergeFields($config, [
			'fields' => [
				"get$baseName" => [
					'type' => TypeRegister::nonNull($outputType),
					'args' => [
						BaseType::ID_NAME => TypeRegister::nonNull(TypeRegister::id()),
					],
					'resolve' => function (array $rootValue, array $args, $context, ResolveInfo $resolveInfo) use ($repository): array {
						if ($this->onBeforeGetOne) {
							[$rootValue, $args] = \call_user_func($this->onBeforeGetOne, $rootValue, $args);
						}

						$results = $this->fetchResult($repository->many()->where('this.' . BaseType::ID_NAME, $args[BaseType::ID_NAME]), $resolveInfo);

						if (!$results) {
							throw new NotFoundException($args[BaseType::ID_NAME]);
						}

						return Arrays::first($results);
					},
				],
				"get{$baseName}s" => [
					'type' => TypeRegister::listOf($outputType),
					'args' => [
						'sort' => $this->typeRegister::string(),
						'order' => $this->typeRegister->orderEnum(),
						'limit' => Type::int(),
						'page' => Type::int(),
						'filters' => $this->typeRegister->JSON(),
					],
					'resolve' => function (array $rootValue, array $args, $context, ResolveInfo $resolveInfo) use ($repository): array {
						if ($this->onBeforeGetAll) {
							[$rootValue, $args] = \call_user_func($this->onBeforeGetAll, $rootValue, $args);
						}

						$collection = $repository->many()
							->orderBy([$args['sort'] ?? $this::DEFAULT_SORT => $args['order'] ?? $this::DEFAULT_ORDER])
							->setPage($args['page'] ?? $this::DEFAULT_PAGE, $args['limit'] ?? $this::DEFAULT_LIMIT);

						try {
							$collection->filter((array) ($args['filters'] ?? []));
						} catch (\Throwable $e) {
							throw new BadRequestException('Invalid filters');
						}

						return $this->fetchResult($collection, $resolveInfo);
					},
				],
			],
		]);

		parent::__construct($container, $config);
	}

	public function getOutputType(): OutputType
	{
		return $this->typeRegister->getOutputType($this->getName());
	}

	/**
	 * @return \StORM\Repository<\StORM\Entity>
	 */
	protected function getRepository(): Repository
	{
		return $this->container->getByType($this->getRepositoryClass());
	}
}
