<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Security;

use Admin\DB\Role;
use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;

class RoleOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Role::class),
		];

		parent::__construct($config);
	}
}
