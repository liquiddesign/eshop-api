<?php

namespace App\TypeRegistries;

use App\Outputs\Eshop\AddressOutput;
use App\Outputs\Eshop\CustomerOutput;

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
}
