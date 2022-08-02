<?php

declare(strict_types=1);

namespace App\Outputs\Security;

use Admin\DB\Role;
use App\Base\BaseOutput;
use App\TypeRegister;

class RoleOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass($this->getSourceClassName()),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Role::class;
	}
}
