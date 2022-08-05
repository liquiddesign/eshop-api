<?php

namespace App\Crud;

use App\Base\BaseMutation;
use App\Base\BaseType;
use App\TypeRegister;
use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\OutputType;
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
						'input' => $this->getUpdateInputType(),
						],
					'resolve' => function (array $rootValue, array $args) use ($repository): Entity {
						if ($this->onBeforeUpdate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeUpdate, $rootValue, $args);
						}

						$input = $args['input'];

						$repository->many()->where('this.' . BaseType::ID_NAME, $input[BaseType::ID_NAME])->update($input);

						return $repository->one($input[BaseType::ID_NAME], true);
					},
				],
				"delete{$baseName}s" => [
					'type' => TypeRegister::nonNull(TypeRegister::int()),
					'args' => [BaseType::ID_NAME => TypeRegister::listOf(TypeRegister::id()),],
					'resolve' => function (array $rootValue, array $args) use ($repository): int {
						if ($this->onBeforeDelete) {
							[$rootValue, $args] = \call_user_func($this->onBeforeDelete, $rootValue, $args);
						}

						return $repository->many()->where('this.' . BaseType::ID_NAME, $args[BaseType::ID_NAME])->delete();
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

	public function getCreateInputType(): InputType
	{
		return $this->typeRegister->getInputType($this->getName() . 'Create');
	}

	public function getUpdateInputType(): InputType
	{
		return $this->typeRegister->getInputType($this->getName() . 'Update');
	}

	/**
	 * @return \StORM\Repository<\StORM\Entity>
	 */
	protected function getRepository(): Repository
	{
		return $this->container->getByType($this->getRepositoryClass());
	}
}
