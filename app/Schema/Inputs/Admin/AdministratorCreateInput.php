<?php

namespace App\Schema\Inputs\Admin;

use Admin\DB\Administrator;
use App\Schema\Base\BaseInput;
use App\Schema\Inputs\InputRelationFieldsEnum;
use App\Schema\TypeRegister;

class AdministratorCreateInput extends BaseInput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createInputFieldsFromClass(
				Administrator::class,
				includeId: false,
				setDefaultValues: true,
				inputRelationFieldsEnum: InputRelationFieldsEnum::ONLY_ADD,
			),
		];

		parent::__construct($config);
	}
}
