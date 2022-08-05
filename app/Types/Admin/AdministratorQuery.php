<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\AdministratorRepository;
use App\Crud\CrudQuery;

class AdministratorQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'administrator';
	}

	public function getRepositoryClass(): string
	{
		return AdministratorRepository::class;
	}
}
