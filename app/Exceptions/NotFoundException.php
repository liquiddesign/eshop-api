<?php

namespace App\Exceptions;

use App\Exceptions\Base\BaseException;
use App\Exceptions\Base\ExceptionCategories;

class NotFoundException extends BaseException
{
	public function __construct(string $id)
	{
		parent::__construct("Object '$id' not found");
	}

	public function getCategory(): string
	{
		return (string) ExceptionCategories::NOT_FOUND->value;
	}
}
