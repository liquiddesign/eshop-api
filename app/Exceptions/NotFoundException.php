<?php

namespace App\Exceptions;

class NotFoundException extends \Exception
{
	public function __construct(string $id)
	{
		parent::__construct("Object '$id' not found");
	}
}
