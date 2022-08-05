<?php

declare(strict_types=1);

namespace App\Outputs\Admin;

use Admin\DB\Administrator;
use App\Base\BaseOutput;
use App\TypeRegister;

class AdministratorOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Administrator::class),
		];

		parent::__construct($config);
	}
}
