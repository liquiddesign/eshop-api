<?php

namespace App\Schema\Base;

use App\Schema\TypeRegister;
use GraphQL\Type\Definition\InputType;
use GraphQL\Type\Definition\NullableType;
use GraphQL\Type\Definition\OutputType;
use GraphQL\Type\Definition\ResolveInfo;
use Nette\DI\Container;
use Nette\Utils\Arrays;
use Nette\Utils\Strings;
use StORM\DIConnection;
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
		/** @var \App\Schema\TypeRegister $typeRegister */
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
					'resolve' => function (array $rootValue, array $args, $context, ResolveInfo $resolveInfo) use ($repository): array {
						if ($this->onBeforeCreate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeCreate, $rootValue, $args);
						}

						foreach ($args['input'] as $inputKey => $inputField) {
							if (Strings::startsWith($inputKey, 'add')) {
								$args['input'][Strings::lower(\substr($inputKey, 3))] = $inputField;
								unset($args['input'][$inputKey]);
							}
						}

						$new = $repository->createOne($args['input']);

						return Arrays::first($this->fetchResult($repository->many()->where('this.' . BaseType::ID_NAME, $new->getPK()), $resolveInfo));
					},
				],
				"update$baseName" => [
					'type' => TypeRegister::nonNull($outputType),
					'args' => [
						'input' => $this->getUpdateInputType(),
						],
					'resolve' => function (array $rootValue, array $args, $context, ResolveInfo $resolveInfo) use ($repository): array {
						if ($this->onBeforeUpdate) {
							[$rootValue, $args] = \call_user_func($this->onBeforeUpdate, $rootValue, $args);
						}

						$input = $args['input'];

						$repository->many()->where('this.' . BaseType::ID_NAME, $input[BaseType::ID_NAME])->update($input);

						return Arrays::first($this->fetchResult($repository->many()->where('this.' . BaseType::ID_NAME, $input[BaseType::ID_NAME]), $resolveInfo));
					},
				],
				"delete{$baseName}s" => [
					'type' => TypeRegister::nonNull(TypeRegister::int()),
					'args' => [BaseType::ID_NAME => TypeRegister::listOf(TypeRegister::id()),],
					'resolve' => function (array $rootValue, array $args, $context, ResolveInfo $resolveInfo) use ($repository): int {
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
}