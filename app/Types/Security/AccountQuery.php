<?php

declare(strict_types=1);

namespace App\Types\Security;

use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;
use Security\DB\AccountRepository;

class AccountQuery extends CrudQuery
{
	public function getName(): string
	{
		return 'account';
	}

	public function getOutputType(): BaseOutput
	{
		return TypeRegister::account();
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
