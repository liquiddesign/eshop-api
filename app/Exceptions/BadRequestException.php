<?php

namespace App\Exceptions;

use App\Exceptions\Base\BaseException;
use App\Exceptions\Base\ExceptionCategories;

class BadRequestException extends BaseException
{
	public function __construct(string $string)
	{
		parent::__construct("Bad request: $string");
	}

	public function isClientSafe(): bool
	{
		return true;
	}

	public function getCategory(): string
	{
		return (string) ExceptionCategories::BAD_REQUEST->value;
	}
}
