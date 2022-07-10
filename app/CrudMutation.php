<?php

namespace App;

use App\Exceptions\NotFoundException;
use GraphQL\Type\Definition\ObjectType;
use Nette\DI\Container;
use Nette\Utils\Strings;
use StORM\Entity;
use StORM\Repository;

/**
 * @method array onBeforeCreate(array $rootValues, array $args)
 * @method array onBeforeUpdate(array $rootValues, array $args)
 * @method array onBeforeDelete(array $rootValues, array $args)
 */
abstract class CrudMutation extends ObjectType implements IMutation
{
	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeCreate = null;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeUpdate = null;

	/** @var callable(array<mixed>, array<mixed>): array<mixed>|null */
	public $onBeforeDelete = null;

	abstract public function getName(): string;

	abstract public function getOutputType(): BaseOutput;

	abstract public function getCreateInputType(): BaseInput;

	abstract public function getUpdateInputType(): BaseInput;

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
				"create$baseName" => [
					'type' => $outputType,
					'args' => ['input' => $this->getCreateInputType(),],
					'resolve' => function (array $rootValue, array $args) use ($repository): Entity {
						if ($this->onBeforeCreate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeCreate, $rootValue, $args);
						}

						return $repository->createOne($args['input']);
					},
				],
				"update$baseName" => [
					'type' => $outputType,
					'args' => ['input' => $this->getUpdateInputType(),],
					'resolve' => function (array $rootValue, array $args) use ($repository): Entity {
						if ($this->onBeforeUpdate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeUpdate, $rootValue, $args);
						}

						try {
							$repository->syncOne($args['input']);
						} catch (\Throwable $e) {
							throw new NotFoundException($args['input'][IType::ID_NAME]);
						}

						return $repository->one($args['input'][IType::ID_NAME], true);
					},
				],
				"delete$baseName" => [
					'type' => TypeRegistry::int(),
					'args' => [IType::ID_NAME => TypeRegistry::id(),],
					'resolve' => function (array $rootValue, array $args) use ($repository): int {
						if ($this->onBeforeDelete) {
							[$rootValue, $args] = \call_user_func($this->onBeforeDelete, $rootValue, $args);
						}

						return ($object = $repository->one($args[IType::ID_NAME])) ? $object->delete() : 0;
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
