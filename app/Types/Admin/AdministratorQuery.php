<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\AdministratorRepository;
use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;
use Nette\DI\Container;

class AdministratorQuery extends CrudQuery
{
	public function __construct(Container $container, private readonly TypeRegister $typeRegister, array $config = [])
	{
		parent::__construct($container, $typeRegister, $config);
	}

	public function getName(): string
	{
		return 'administrator';
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->administrator();
	}

	public function getRepositoryClass(): string
	{
		return AdministratorRepository::class;
	}
}
