<?php

declare(strict_types=1);

namespace App\Types\Security;

use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;
use Nette\DI\Container;
use Security\DB\AccountRepository;

class AccountQuery extends CrudQuery
{
	public function __construct(Container $container, private readonly TypeRegister $typeRegister, array $config = [])
	{
		parent::__construct($container, $typeRegister, $config);
	}

	public function getName(): string
	{
		return 'account';
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->account();
	}

	public function getRepositoryClass(): string
	{
		return AccountRepository::class;
	}
}
