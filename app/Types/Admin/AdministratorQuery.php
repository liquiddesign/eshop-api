<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\Administrator;
use App\Crud\CrudQuery;

class AdministratorQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
