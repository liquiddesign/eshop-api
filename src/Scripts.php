<?php

namespace EshopApi;

use Composer\Script\Event;
use Migrator\Migrator;
use Nette\Configurator;
use Nette\DI\Container;
use Nette\Utils\Strings;
use Security\DB\Account;
use StORM\DIConnection;

class Scripts extends \Base\Scripts
{
	/**
	 * Trigger as event from composer
	 * @param \Composer\Script\Event $event Composer event
	 */
	public static function syncDatabase(Event $event): void
	{
		$arguments = $event->getArguments();

		$event->getIO()->write($arguments);

		$container = static::getDIContainer($arguments);

		$migrator = $container->getByType(Migrator::class);
		$sql = $migrator->dumpAlters();
		$event->getIO()->write($sql);

		if (!Strings::trim($sql)) {
			$event->getIO()->write('Everything is synchronized. Good job!');

			return;
		}

		if (!$event->getIO()->askConfirmation('Execute SQL command? (n)', false)) {
			return;
		}

		$container->getByType(DIConnection::class)->query($sql);

		$sql = $migrator->dumpAlters();

		if (!Strings::trim($sql)) {
			$event->getIO()->write('Everything is synchronized. Good job!');
		} else {
			$event->getIO()->writeError(' Synchronization failed!');
		}
	}

	/**
	 * @param array<mixed> $arguments
	 */
	protected static function getDIContainer(array $arguments): Container
	{
		if (isset($arguments[0]) && \is_file(\dirname(__DIR__, 4) . '/' . $arguments[0])) {
			return require_once \dirname(__DIR__, 4) . '/' . $arguments[0];
		}

		$class = isset($arguments[0]) && \class_exists($arguments[0]) ? $arguments[0] : '\EshopApi\Bootstrap';

		return \method_exists($class, 'createContainer') ? $class::createContainer() : $class::boot()->createContainer();
	}

	protected static function createConfigurator(): Configurator
	{
		/** @var \Nette\Configurator $configurator */
		$configurator = Bootstrap::boot();

		return $configurator;
	}
	
	protected static function getAccountEntityClass(): string
	{
		return Account::class;
	}
	
	protected static function getRootDirectory(): string
	{
		return __DIR__ . '/..';
	}
}
