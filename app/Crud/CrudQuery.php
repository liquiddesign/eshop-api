<?php

namespace App\Crud;

use App\Base\BaseOutput;
use App\Base\BaseQuery;
use App\Base\BaseType;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\TypeRegister;
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

	abstract public function getName(): string;

	abstract public function getOutputType(): BaseOutput;

	/**
	 * @return class-string
	 */
	abstract public function getRepositoryClass(): string;

	public function __construct(protected Container $container, array $config = [])
	{
		$baseName = Strings::firstUpper($this->getName());
		$outputType = $this->getOutputType();
		$repository = $this->getRepository();

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
						'sort' => Type::string(),
						'order' => TypeRegister::orderEnum(),
						'limit' => Type::int(),
						'page' => Type::int(),
						'filters' => TypeRegister::JSON(),
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

						$result = $this->fetchResult($collection, $resolveInfo);

						\dump(\memory_get_peak_usage(true)/1024/1024);

						return $result;
					},
				],
			],
		]);

		parent::__construct($container, $config);
	}

	/**
	 * @return \StORM\Repository<\StORM\Entity>
	 */
	protected function getRepository(): Repository
	{
		return $this->container->getByType($this->getRepositoryClass());
	}
}
