<?php

namespace App\Resolvers\Exceptions;

use App\Resolvers\Exceptions\Base\BaseException;
use App\Resolvers\Exceptions\Base\ExceptionCategories;

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
