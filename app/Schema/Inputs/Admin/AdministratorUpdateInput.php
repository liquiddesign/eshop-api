<?php

namespace App\Schema\Inputs\Admin;

use Admin\DB\Administrator;
use App\Schema\Base\BaseInput;
use App\Schema\TypeRegister;

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
