<?php

namespace App;

use GraphQL\Type\Definition\ObjectType;

abstract class BaseOutput extends ObjectType
{
	/**
	 * @return class-string
	 */
	abstract public function getSourceClassName(): string;

	/**
	 * @return array<string>
	 */
	abstract public function getRelations(): array;
}
