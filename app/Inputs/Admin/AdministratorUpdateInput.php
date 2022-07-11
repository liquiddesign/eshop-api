<?php

namespace App\Inputs\Admin;

use Admin\DB\Administrator;
use App\Base\BaseInput;
use App\TypeRegister;

class AdministratorUpdateInput extends BaseInput
{
	public function __construct()
	{
		$config = [
			'fields' => TypeRegister::createFieldsFromClass($this->getSourceClassName(), forceAllOptional: true),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Administrator::class;
	}
}
