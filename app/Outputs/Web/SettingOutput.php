<?php

declare(strict_types=1);

namespace App\Outputs\Web;

use App\Base\BaseOutput;
use App\TypeRegister;
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
