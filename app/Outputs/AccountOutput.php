<?php

declare(strict_types=1);

namespace App\Outputs;

use App\Base\BaseOutput;
use App\TypeRegistry;
use Security\DB\Account;

class AccountOutput extends BaseOutput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegistry::createFieldsFromClass($this->getSourceClassName(), exclude: ['password']),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Account::class;
	}

	/**
	 * @inheritDoc
	 */
	public function getRelations(): array
	{
		return [];
	}
}
