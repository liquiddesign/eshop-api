<?php

declare(strict_types=1);

namespace App\Schema\Outputs\Web;

use App\Schema\Base\BaseOutput;
use App\Schema\TypeRegister;
use Web\DB\Setting;

class SettingOutput extends BaseOutput
{
	public function __construct(TypeRegister $typeRegister)
	{
		$config = [
			'fields' => $typeRegister->createOutputFieldsFromClass(Setting::class),
		];

		parent::__construct($config);
	}
}
