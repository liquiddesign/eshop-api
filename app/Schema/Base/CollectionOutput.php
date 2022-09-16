<?php

namespace App\Schema\Base;

use App\Schema\TypeRegister;

abstract class CollectionOutput extends BaseOutput
{
	public function __construct(array $config, TypeRegister $typeRegister)
	{
		if (!isset($config['fields'])) {
			parent::__construct($config);

			return;
		}

		$collectionInterface = $typeRegister->getManyOutputInterface();

		$config['fields'] = \array_merge($config['fields'], [
			$collectionInterface->getField('onPageCount'),
			$collectionInterface->getField('totalCount'),
		]);

		$config['interfaces'] = [
			$collectionInterface,
		];

		parent::__construct($config);
	}
}
