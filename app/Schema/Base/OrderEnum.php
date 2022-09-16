<?php

namespace App\Schema\Base;

use GraphQL\Type\Definition\EnumType;

class OrderEnum extends EnumType
{
	public function __construct()
	{
		$config = [
			'values' => ['ASC', 'DESC'],
		];

		parent::__construct($config);
	}
}
