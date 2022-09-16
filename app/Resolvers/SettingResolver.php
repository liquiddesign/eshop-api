<?php

namespace App\Resolvers;

use App\Crud\CrudResolver;
use Web\DB\Setting;

class SettingResolver extends CrudResolver
{
	public function getClass(): string
	{
		return Setting::class;
	}
}
