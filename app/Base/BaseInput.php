<?php

namespace App\Base;

use GraphQL\Type\Definition\InputObjectType;

abstract class BaseInput extends InputObjectType
{
	/**
	 * @return class-string
	 */
	abstract public function getSourceClassName(): string;
}