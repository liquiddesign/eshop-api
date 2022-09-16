<?php

declare(strict_types=1);

namespace App\Schema\Types\Admin;

use Admin\DB\Administrator;
use App\Schema\Base\CrudQuery;

class AdministratorQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
