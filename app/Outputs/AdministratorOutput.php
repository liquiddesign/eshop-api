<?php

declare(strict_types=1);

namespace App\Outputs;

use Admin\DB\Administrator;
use App\Base\BaseOutput;
use App\TypeRegistry;

class AdministratorOutput extends BaseOutput
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
		return Administrator::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getRelations(): array
	{
		return ['accounts'];
	}
}
