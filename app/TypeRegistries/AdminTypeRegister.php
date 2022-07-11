<?php

namespace App\TypeRegistries;

use App\Inputs\Admin\AdministratorCreateInput;
use App\Inputs\Admin\AdministratorUpdateInput;
use App\Outputs\Admin\AdministratorOutput;

trait AdminTypeRegister
{
	public static function administrator(): AdministratorOutput
	{
		return static::$types['administrator'] ??= new AdministratorOutput();
	}

	public static function administratorCreate(): AdministratorCreateInput
	{
		return static::$types['administratorCreate'] ??= new AdministratorCreateInput();
	}

	public static function administratorUpdate(): AdministratorUpdateInput
	{
		return static::$types['administratorUpdate'] ??= new AdministratorUpdateInput();
	}
}
