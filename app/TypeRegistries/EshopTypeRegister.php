<?php

namespace App\TypeRegistries;

use App\Outputs\Eshop\AddressOutput;
use App\Outputs\Eshop\CustomerGroupOutput;
use App\Outputs\Eshop\CustomerOutput;
use App\Outputs\Eshop\PricelistOutput;

trait EshopTypeRegister
{
	public static function customer(): CustomerOutput
	{
		return static::$types['customer'] ??= new CustomerOutput();
	}

	public static function address(): AddressOutput
	{
		return static::$types['address'] ??= new AddressOutput();
	}

	public static function customerGroup(): CustomerGroupOutput
	{
		return static::$types['customerGroup'] ??= new CustomerGroupOutput();
	}

	public static function pricelist(): PricelistOutput
	{
		return static::$types['pricelist'] ??= new PricelistOutput();
	}
}
