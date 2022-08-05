<?php

declare(strict_types=1);

namespace App\Types\Security;

use App\Crud\CrudQuery;
use Security\DB\AccountRepository;

class AccountQuery extends CrudQuery
{
	/** @TODO pouze funkce getClass s class-string, odtud vzít jméno a repo, původní funkce nechat s defaultní funkcionalitou pro možnost override */
	public function getName(): string
	{
		return 'account';
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
