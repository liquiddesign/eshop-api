<?php

namespace App\TypeRegistries;

use App\Inputs\Security\AccountCreateInput;
use App\Inputs\Security\AccountUpdateInput;
use App\Outputs\Security\AccountOutput;
use App\Outputs\Security\RoleOutput;

trait SecurityTypeRegister
{
	public function account(): AccountOutput
	{
		return $this->types['account'] ??= new AccountOutput($this);
	}

	public function accountCreateInput(): AccountCreateInput
	{
		return $this->types['accountCreate'] ??= new AccountCreateInput($this);
	}

	public function accountUpdateInput(): AccountUpdateInput
	{
		return $this->types['accountUpdate'] ??= new AccountUpdateInput($this);
	}

	public function role(): RoleOutput
	{
		return $this->types['role'] ??= new RoleOutput($this);
	}
}
