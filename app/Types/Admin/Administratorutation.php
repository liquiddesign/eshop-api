<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\AdministratorRepository;
use App\Base\BaseInput;
use App\Base\BaseOutput;
use App\Crud\CrudMutation;
use App\TypeRegister;

class Administratorutation extends CrudMutation
{
	public function getName(): string
	{
		return 'administrator';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegister::administrator();
	}

	public function getRepositoryClass(): string
	{
		return AdministratorRepository::class;
	}

	public function getCreateInputType(): BaseInput
	{
		return TypeRegister::administratorCreate();
	}

	public function getUpdateInputType(): BaseInput
	{
		return TypeRegister::administratorUpdate();
	}
}
