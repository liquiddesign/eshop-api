<?php

namespace App\TypeRegistries;

use App\Inputs\Eshop\CustomerCreateInput;
use App\Inputs\Eshop\CustomerUpdateInput;
use App\Outputs\Eshop\AddressOutput;
use App\Outputs\Eshop\CustomerGroupOutput;
use App\Outputs\Eshop\CustomerOutput;
use App\Outputs\Eshop\PricelistOutput;

trait EshopTypeRegister
{
	public function customer(): CustomerOutput
	{
		return $this->types['customer'] ??= new CustomerOutput($this);
	}

	public function customerCreateInput(): CustomerCreateInput
	{
		return $this->types['customerCreate'] ??= new CustomerCreateInput($this);
	}

	public function customerUpdateInput(): CustomerUpdateInput
	{
		return $this->types['customerUpdate'] ??= new CustomerUpdateInput($this);
	}

	public function address(): AddressOutput
	{
		return $this->types['address'] ??= new AddressOutput($this);
	}

	public function customerGroup(): CustomerGroupOutput
	{
		return $this->types['customerGroup'] ??= new CustomerGroupOutput($this);
	}

	public function pricelist(): PricelistOutput
	{
		return $this->types['pricelist'] ??= new PricelistOutput($this);
	}
}
