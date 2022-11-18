<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace EshopApi\Schema\Outputs;

class ProductOutput extends \LqGrAphi\Schema\BaseOutput implements \LqGrAphi\Schema\ClassOutput
{
	public function __construct(\LqGrAphi\Schema\TypeRegister $typeRegister)
	{
		parent::__construct([
			'fields' => $typeRegister->createOutputFieldsFromClass(\Eshop\DB\Product::class),
		]);
	}

	/**
	 * @return class-string<\StORM\Entity>
	 */
	public static function getClass(): string
	{
		return \Eshop\DB\Product::class;
	}
}
