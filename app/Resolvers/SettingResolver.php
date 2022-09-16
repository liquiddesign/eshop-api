<?php

namespace App\Resolvers;

use App\Resolvers\Base\CrudResolver;
use Web\DB\Setting;

class SettingResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Setting::class;
	}
}
