<?php

declare(strict_types=1);

namespace App\Schema\Types\Web;

use App\Schema\Base\CrudQuery;
use Web\DB\Setting;

class SettingQuery extends CrudQuery
{
	public function getClass(): string
	{
		return Setting::class;
	}
}
