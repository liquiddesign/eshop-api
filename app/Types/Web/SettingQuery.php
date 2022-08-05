<?php

declare(strict_types=1);

namespace App\Types\Web;

use App\Crud\CrudQuery;
use Web\DB\SettingRepository;

class SettingQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'setting';
	}

	public function getRepositoryClass(): string
	{
		return SettingRepository::class;
	}
}
