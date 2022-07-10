<?php

namespace App\Base;

use GraphQL\Type\Definition\ObjectType;
use Nette\DI\Container;

abstract class BaseType extends ObjectType
{
	public const ID_NAME = 'uuid';

	public function __construct(array $config, protected Container $container)
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
		if (!isset($config1['fields']) || !isset($config2['fields'])) {
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
}
