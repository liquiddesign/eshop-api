<?php

namespace App\Exceptions;


class NotFoundException extends BaseException
{
	public function __construct(string $id)
	{
		parent::__construct("Object '$id' not found");
	}

	public function isClientSafe(): bool
	{
		return true;
	}

	public function getCategory(): ExceptionCategories
	{
		return ExceptionCategories::NOT_FOUND;
	}
}
