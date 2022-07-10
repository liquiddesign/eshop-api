<?php

declare(strict_types=1);

namespace App\Outputs\Security;

use Admin\DB\Role;
use App\Base\BaseOutput;
use App\TypeRegister;

class RoleOutput extends BaseOutput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName()),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Role::class;
	}
}
