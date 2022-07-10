<?php

namespace App;

use GraphQL\Type\Definition\InputObjectType;

abstract class BaseInput extends InputObjectType
{
	/**
	 * @return class-string
	 */
	abstract public function getSourceClassName(): string;

	public function validate(BaseInput $input): bool
	{
		return true;
	}
}
