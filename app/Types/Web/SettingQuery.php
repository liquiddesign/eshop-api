<?php

declare(strict_types=1);

namespace App\Types\Web;

use App\Crud\CrudQuery;
use Web\DB\Setting;

class SettingQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Setting::class;
	}
}
