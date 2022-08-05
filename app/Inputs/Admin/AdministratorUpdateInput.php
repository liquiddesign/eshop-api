<?php

namespace App\Inputs\Admin;

use Admin\DB\Administrator;
use App\Base\BaseInput;
use App\TypeRegister;

class AdministratorUpdateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(Administrator::class, forceAllOptional: true),
		];

		parent::__construct($config);
	}
}
