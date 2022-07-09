<?php

declare(strict_types=1);

namespace App\Types;

use Admin\DB\AdministratorRepository;
use App\BaseOutput;
use App\CrudQuery;
use App\TypeRegistry;

class AdministratorQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'administrator';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegistry::administrator();
	}

	public function getRepositoryClass(): string
	{
		return AdministratorRepository::class;
	}
}
