<?php

namespace App\Inputs\Admin;

use Admin\DB\Administrator;
use App\Base\BaseInput;
use App\TypeRegister;

class AdministratorCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(Administrator::class, includeId: false),
		];

		parent::__construct($config);
	}
}
