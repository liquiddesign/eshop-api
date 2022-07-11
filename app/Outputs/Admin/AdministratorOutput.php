<?php

declare(strict_types=1);

namespace App\Outputs\Admin;

use Admin\DB\Administrator;
use App\Base\BaseOutput;
use App\TypeRegister;

class AdministratorOutput extends BaseOutput
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
		return Administrator::class;
	}
}
