<?php

declare(strict_types=1);

namespace App\Schema\Types\Admin;

use Admin\DB\Administrator;
use App\Schema\Base\CrudMutation;

class AdministratorMutation extends CrudMutation
{
	public function getClass(): string
	{
		return Administrator::class;
	}
}
