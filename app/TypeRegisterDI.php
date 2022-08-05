<?php

declare(strict_types=1);

namespace App;

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
					return \count($value) === 3;
				}, 'CRUD type have to has exactly 3 classes!')),
			]),
		]);
	}

	public function loadConfiguration(): void
	{
		$config = (array) $this->getConfig();

		$builder = $this->getContainerBuilder();

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

		if (!isset($config['types']->crud)) {
			return;
		}

		foreach ($config['types']->crud as $name => $types) {
			$typeRegister->addSetup('set', ["{$name}Output", $types[0]]);
			$typeRegister->addSetup('set', ["{$name}CreateInput", $types[1]]);
			$typeRegister->addSetup('set', ["{$name}UpdateInput", $types[2]]);
		}
	}
}
