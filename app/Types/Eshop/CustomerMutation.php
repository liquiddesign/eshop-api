<?php

namespace App\Types\Eshop;

use App\Base\BaseInput;
use App\Base\BaseOutput;
use App\Crud\CrudMutation;
use App\TypeRegister;
use Eshop\DB\CustomerRepository;
use Nette\DI\Container;

class CustomerMutation extends CrudMutation
{
	public function __construct(Container $container, private readonly TypeRegister $typeRegister, array $config = [])
	{
		parent::__construct($container, $config);
	}

	public function getName(): string
	{
		return 'customer';
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->customer();
	}

	public function getCreateInputType(): BaseInput
	{
		return $this->typeRegister->customerCreateInput();
	}

	public function getUpdateInputType(): BaseInput
	{
		return $this->typeRegister->customerUpdateInput();
	}

	public function getRepositoryClass(): string
	{
		return CustomerRepository::class;
	}
}
