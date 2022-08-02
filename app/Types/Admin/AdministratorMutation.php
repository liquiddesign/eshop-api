<?php

declare(strict_types=1);

namespace App\Types\Admin;

use Admin\DB\AdministratorRepository;
use App\Base\BaseInput;
use App\Base\BaseOutput;
use App\Crud\CrudMutation;
use App\TypeRegister;
use Nette\DI\Container;

class AdministratorMutation extends CrudMutation
{
	public function __construct(Container $container, private readonly TypeRegister $typeRegister, array $config = [])
	{
		parent::__construct($container, $config);
	}

	public function getName(): string
	{
		return 'administrator';
	}

	public function getRepositoryClass(): string
	{
		return AdministratorRepository::class;
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->administrator();
	}

	public function getCreateInputType(): BaseInput
	{
		return $this->typeRegister->administratorCreateInput();
	}

	public function getUpdateInputType(): BaseInput
	{
		return $this->typeRegister->administratorUpdateInput();
	}
}
