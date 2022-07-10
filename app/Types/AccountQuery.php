<?php

declare(strict_types=1);

namespace App\Types;

use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegistry;
use Security\DB\AccountRepository;

class AccountQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'account';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegistry::account();
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
