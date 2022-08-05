<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\Administrator;
use App\Crud\CrudMutation;

class AdministratorMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
