<?php

declare(strict_types=1);

namespace App\Types\Security;

use Admin\DB\AdministratorRepository;
use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;

class AdministratorQuery extends CrudQuery
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
}
