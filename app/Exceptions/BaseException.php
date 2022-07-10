<?php

namespace App\Exceptions;

use GraphQL\Error\ClientAware;

abstract class BaseException extends \Exception implements ClientAware
{
	public function __construct(string $message)
	{
		parent::__construct($message, (int) $this->getCategory());
	}
}
