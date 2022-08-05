<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\AdministratorRepository;
use App\Crud\CrudMutation;

class AdministratorMutation extends CrudMutation
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
