<?php

declare(strict_types=1);

namespace App\Outputs;

use Admin\DB\Role;
use App\BaseOutput;
use App\TypeRegistry;

class RoleOutput extends BaseOutput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegistry::createFieldsFromClass($this->getSourceClassName()),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Role::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getRelations(): array
	{
		return [];
	}
}
