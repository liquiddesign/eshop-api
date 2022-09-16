<?php

declare(strict_types=1);

namespace App;

use App\Base\BaseMutation;
use App\Base\BaseQuery;
use App\Exceptions\BadRequestException;
use ArrayAccess;
use Closure;
use Contributte\Psr7\Psr7RequestFactory;
use GraphQL\Error\DebugFlag;
use GraphQL\Language\Parser;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Utils\SchemaPrinter;
use Nette\DI\Container;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Tracy\ILogger;

class GraphQL
{
	public function __construct(private readonly Container $container)
	{
	}

	/**
	 * @return array<mixed>
	 * @throws \Throwable
	 */
	public function executeServer(): array
	{
		try {
			/** @var \Nette\Http\Request $httpRequest */
			$httpRequest = $this->container->getByType(\Nette\Http\Request::class);

			$psrRequest = Psr7RequestFactory::fromNette($httpRequest);

			Debugger::log('csb:' . Debugger::timer());
			$schema = $this->getCachedSchema();
			Debugger::log('csa:' . Debugger::timer());

			$server = new StandardServer([
				'schema' => $schema,
				'queryBatching' => true,
				'fieldResolver' => function ($objectValue, array $args, $context, ResolveInfo $info) {
					$fieldName = $info->fieldName;

					$matchedFieldName = \preg_split('~^[^A-Z]+\K|[A-Z][^A-Z]+\K~', $fieldName, 0, \PREG_SPLIT_NO_EMPTY);

					if (!$matchedFieldName) {
						throw new BadRequestException("Query '$fieldName' not matched!");
					}

					/** @var class-string $resolverName */
					$resolverName = 'App\\Resolvers\\' . Strings::firstUpper(Strings::lower($matchedFieldName[0])) . 'Resolver';

					/** @var \App\BaseResolver|null $resolver */
					$resolver = $this->container->getByType($resolverName, false);

					if (!$resolver) {
						$property = null;

						if (\is_array($objectValue) || $objectValue instanceof ArrayAccess) {
							if (isset($objectValue[$fieldName])) {
								$property = $objectValue[$fieldName];
							}
						} elseif (\is_object($objectValue)) {
							if (isset($objectValue->{$fieldName})) {
								$property = $objectValue->{$fieldName};
							}
						}

						return $property instanceof Closure
							? $property($objectValue, $args, $context, $info)
							: $property;
					}

					unset($matchedFieldName[0]);

					if (\count($matchedFieldName) === 0) {
						return null;
					}

					$actionName = Strings::firstLower(\implode('', $matchedFieldName));

					return $resolver->{$actionName}([], $args, $context, $info);
				},
			]);

			Debugger::log('sa:' . Debugger::timer());

			/** @var \GraphQL\Executor\ExecutionResult $result */
			$result = $server->executePsrRequest($psrRequest);

			Debugger::log('ea:' . Debugger::timer());

			if ($debugFlag = $this->getDebugFlag()) {
				/** @var \StORM\Bridges\StormTracy<\stdClass>|null $stormTracy */
				$stormTracy = Debugger::getBar()->getPanel('StORM\Bridges\StormTracy');

				if ($stormTracy) {
					Debugger::log('After request:' . Debugger::timer());
					Debugger::log('Storm total time:' . $stormTracy->getTotalTime());
					Debugger::log('Storm total queries:' . $stormTracy->getTotalQueries());
				} else {
					Debugger::log('Debug mode is enabled, but StormDebugBar not found!', ILogger::WARNING);
				}
			}

			$result = $result->toArray($debugFlag);

			Debugger::log('ra:' . Debugger::timer());

			return $result;
		} catch (\Throwable $e) {
			if ($this->container->getParameters()['debugMode'] && !$this->container->getParameters()['productionMode']) {
				throw $e;
			}

			return [
				'error' => [
					'message' => $e->getMessage(),
				],
			];
		}
	}

	public function getCachedSchema(): Schema
	{
		$cacheDir = $this->container->getParameters()['tempDir'] . '/cache/graphql';

		$cacheFilename = $cacheDir . '/cached_schema.php';

		if (!\file_exists($cacheFilename)) {
			$schemaString = SchemaPrinter::doPrint($this->getSchema());
			FileSystem::write($cacheDir . '/schema.gql', $schemaString);

			$document = Parser::parse($schemaString);
			FileSystem::write($cacheFilename, "<?php\nreturn " . \var_export(AST::toArray($document), true) . ";\n");
		} else {
			/** @var \GraphQL\Language\AST\DocumentNode $document */
			$document = AST::fromArray(require $cacheFilename);
		}

		$typeConfigDecorator = null;

		return BuildSchema::build($document, $typeConfigDecorator);
	}

	public function getSchema(): Schema
	{
		$queries = $this->container->findByType(BaseQuery::class);
		$mutations = $this->container->findByType(BaseMutation::class);

		$queryFields = [];
		$mutationFields = [];

		foreach ($queries as $query) {
			/** @var \GraphQL\Type\Definition\ObjectType $queryType */
			$queryType = $this->container->getByName($query);

			foreach ($queryType->getFields() as $field) {
				if (isset($queryFields[$field->getName()])) {
					throw new \Exception("Query '$field->name' already exists!");
				}

				$queryFields[$field->getName()] = $field;
			}
		}

		foreach ($mutations as $mutation) {
			/** @var \GraphQL\Type\Definition\ObjectType $queryType */
			$queryType = $this->container->getByName($mutation);

			foreach ($queryType->getFields() as $field) {
				if (isset($queryFields[$field->getName()])) {
					throw new \Exception("Mutation '$field->name' already exists!");
				}

				$mutationFields[$field->getName()] = $field;
			}
		}

		$schema = [];

		if ($queryFields) {
			$schema['query'] = new ObjectType([
				'name' => 'Query',
				'fields' => $queryFields,
			]);
		}

		if ($mutationFields) {
			$schema['mutation'] = new ObjectType([
				'name' => 'Mutation',
				'fields' => $mutationFields,
			]);
		}

		return new Schema($schema);
	}

	public function getDebugFlag(): int
	{
		$debug = DebugFlag::NONE;

		if ($this->container->getParameters()['debugMode'] && !$this->container->getParameters()['productionMode']) {
			Debugger::log('debugMode');
			$debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
		}

		return $debug;
	}
}
