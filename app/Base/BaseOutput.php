<?php

namespace App\Base;

use GraphQL\Type\Definition\ObjectType;

abstract class BaseOutput extends ObjectType
{
	/**
	 * @return class-string<\StORM\Entity>
	 */
	abstract public function getSourceClassName(): string;

	public function getCreateInputType(): ?BaseInput
	{
		return null;
	}

	public function getUpdateInputType(): ?BaseInput
	{
		return null;
	}
}
