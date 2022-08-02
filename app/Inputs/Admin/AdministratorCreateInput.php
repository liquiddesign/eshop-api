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
			'fields' => $typeRegister->createInputFieldsFromClass($this->getSourceClassName(), includeId: false),
		];

		parent::__construct($config);
	}

	public function getSourceClassName(): string
	{
		return Administrator::class;
	}
}
