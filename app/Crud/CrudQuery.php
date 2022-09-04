<?php

namespace App\Crud;

use App\Base\BaseQuery;
use App\Base\BaseType;
use App\Exceptions\BadRequestException;
use App\TypeRegister;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Nette\DI\Container;
use Nette\Utils\Strings;
use StORM\DIConnection;
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

	/**
	 * @var \StORM\Repository<\StORM\Entity>
	 */
	private Repository $repository;

	/**
	 * @return class-string<\StORM\Entity>
	 */
	abstract public function getClass(): string;

	public function __construct(protected Container $container, array $config = [])
	{
		$this->typeRegister = $this->container->getByType(TypeRegister::class);

		$baseName = $this->getName();
		$outputType = $this->getOutputType();

		\assert($outputType instanceof NullableType);
		\assert($outputType instanceof Type);

		$config = $this->mergeFields($config, [
			'fields' => [
				"{$baseName}One" => [
					'type' => $outputType,
					'args' => [
						BaseType::ID_NAME => TypeRegister::nonNull(TypeRegister::id()),
					],
				],
				"{$baseName}All" => [
					'type' => TypeRegister::nonNull(TypeRegister::listOf($outputType)),
					'args' => [
						'sort' => $this->typeRegister::string(),
						'order' => $this->typeRegister->orderEnum(),
						'limit' => TypeRegister::int(),
						'page' => TypeRegister::int(),
						'filters' => $this->typeRegister->JSON(),
					],
				],
			],
		]);

		parent::__construct($container, $config);
	}

	public function getName(): string
	{
		$reflection = new \ReflectionClass($this->getClass());

		return Strings::lower($reflection->getShortName());
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
		return $this->repository ??= $this->container->getByType(DIConnection::class)->findRepository($this->getClass());
	}
}
