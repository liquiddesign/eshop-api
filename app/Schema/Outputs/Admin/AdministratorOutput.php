<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Admin;

use Admin\DB\Administrator;
use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;

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