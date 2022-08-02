<?php

namespace App\Crud;

use App\Base\BaseInput;
use App\Base\BaseMutation;
use App\Base\BaseOutput;
use App\Base\BaseType;
use App\Exceptions\NotFoundException;
use App\TypeRegister;
use Nette\DI\Container;
use Nette\Utils\Strings;
use StORM\Entity;
use StORM\Repository;

/**
 * @method array onBeforeCreate(array $rootValues, array $args)
 * @method array onBeforeUpdate(array $rootValues, array $args)
 * @method array onBeforeDelete(array $rootValues, array $args)
 */
abstract class CrudMutation extends BaseMutation
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

	public function __construct(protected Container $container, array $config = [])
	{
		$baseName = Strings::firstUpper($this->getName());
		$outputType = $this->getOutputType();
		$repository = $this->getRepository();

		$config = $this->mergeFields($config, [
			'fields' => [
				"create$baseName" => [
					'type' => TypeRegister::nonNull($outputType),
					'args' => ['input' => $this->getCreateInputType(),],
					'resolve' => function (array $rootValue, array $args) use ($repository): Entity {
						if ($this->onBeforeCreate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeCreate, $rootValue, $args);
						}

						return $repository->createOne($args['input']);
					},
				],
				"update$baseName" => [
					'type' => TypeRegister::nonNull($outputType),
					'args' => [
						BaseType::ID_NAME => TypeRegister::nonNull(TypeRegister::id()),
						'input' => $this->getUpdateInputType(),
						],
					'resolve' => function (array $rootValue, array $args) use ($repository): Entity {
						if ($this->onBeforeUpdate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeUpdate, $rootValue, $args);
						}

						$input = $args['input'];
						$input[BaseType::ID_NAME] = $args['id'];

						try {
							$repository->syncOne($input);
						} catch (\Throwable $e) {
							throw new NotFoundException($input[BaseType::ID_NAME]);
						}

						return $repository->one($input[BaseType::ID_NAME], true);
					},
				],
				"delete$baseName" => [
					'type' => TypeRegister::nonNull(TypeRegister::int()),
					'args' => [BaseType::ID_NAME => TypeRegister::id(),],
					'resolve' => function (array $rootValue, array $args) use ($repository): int {
						if ($this->onBeforeDelete) {
							[$rootValue, $args] = \call_user_func($this->onBeforeDelete, $rootValue, $args);
						}

						return ($object = $repository->one($args[BaseType::ID_NAME])) ? $object->delete() : 0;
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
