<?php

declare(strict_types=1);

namespace App\Types\Eshop;

use App\Base\BaseOutput;
use App\Crud\CrudQuery;
use App\TypeRegister;
use Eshop\DB\CustomerRepository;
use Nette\DI\Container;

class CustomerQuery extends CrudQuery
{
	public function __construct(Container $container, private readonly TypeRegister $typeRegister, array $config = [])
	{
		parent::__construct($container, $typeRegister, $config);
	}

	public function getName(): string
	{
		return 'customer';
	}

	public function getOutputType(): BaseOutput
	{
		return $this->typeRegister->customer();
	}

	public function getRepositoryClass(): string
	{
		return CustomerRepository::class;
	}
}
