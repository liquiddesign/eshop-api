<?php

declare(strict_types=1);

namespace App\Schema;

use Nette\Schema\Expect;
use Nette\Schema\Schema;

/**
 * @package App\Eshop
 */
class TypeRegisterDI extends \Nette\DI\CompilerExtension
{
	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'types' => Expect::structure([
				'output' => Expect::arrayOf(Expect::string()),
				'input' => Expect::arrayOf(Expect::string()),
				'crud' => Expect::arrayOf(Expect::listOf(Expect::string())->assert(function ($value) {
					return \count($value) >= 1 && \count($value) <= 3;
				}, 'CRUD type have to has 1-3 classes!')),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$config = (array) $this->getConfig();

		$builder = $this->getContainerBuilder();

		//@TODO nenačítat Query a Mutace v configu ale jen na vyžádání pokud je potřeba sestrojit schéma
		$typeRegister = $builder->addDefinition($this->prefix('typeRegister'))->setType(TypeRegister::class);

		if (isset($config['types']->output)) {
			foreach ($config['types']->output as $name => $type) {
				$typeRegister->addSetup('set', ["{$name}Output", $type]);
			}
		}

		if (isset($config['types']->input)) {
			foreach ($config['types']->input as $name => $type) {
				$typeRegister->addSetup('set', ["{$name}Input", $type]);
			}
		}

		foreach ($config['types']?->crud ?? [] as $name => $types) {
			if (!isset($types[0])) {
				throw new \Exception('Crud type has to have at least output type!');
			}

			$typeRegister->addSetup('set', ["{$name}Output", $types[0]]);

			if (!isset($types[1])) {
				continue;
			}

			$typeRegister->addSetup('set', ["{$name}CreateInput", $types[1]]);

			if (!isset($types[2])) {
				continue;
			}

			$typeRegister->addSetup('set', ["{$name}UpdateInput", $types[2]]);
		}
	}
}