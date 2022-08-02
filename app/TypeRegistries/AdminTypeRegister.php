<?php

namespace App\TypeRegistries;

use App\Inputs\Admin\AdministratorCreateInput;
use App\Inputs\Admin\AdministratorUpdateInput;
use App\Outputs\Admin\AdministratorOutput;

trait AdminTypeRegister
{
	public function administrator(): AdministratorOutput
	{
		return $this->types['administrator'] ??= new AdministratorOutput($this);
	}

	public function administratorCreateInput(): AdministratorCreateInput
	{
		return $this->types['administratorCreate'] ??= new AdministratorCreateInput($this);
	}

	public function administratorUpdateInput(): AdministratorUpdateInput
	{
		return $this->types['administratorUpdate'] ??= new AdministratorUpdateInput($this);
	}
}
