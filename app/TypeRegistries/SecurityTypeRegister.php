<?php

namespace App\TypeRegistries;

use App\Inputs\Security\AccountCreateInput;
use App\Inputs\Security\AccountUpdateInput;
use App\Outputs\Security\AccountOutput;
use App\Outputs\Security\RoleOutput;

trait SecurityTypeRegister
{
	public static function account(): AccountOutput
	{
		return static::$types['account'] ??= new AccountOutput();
	}

	public static function accountCreate(): AccountCreateInput
	{
		return static::$types['accountCreate'] ??= new AccountCreateInput();
	}

	public static function accountUpdate(): AccountUpdateInput
	{
		return static::$types['accountUpdate'] ??= new AccountUpdateInput();
	}

	public static function role(): RoleOutput
	{
		return static::$types['role'] ??= new RoleOutput();
	}
}
