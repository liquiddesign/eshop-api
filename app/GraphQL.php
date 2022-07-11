<?php

declare(strict_types=1);

namespace App;

use App\Base\BaseMutation;
use App\Base\BaseQuery;
use Contributte\Psr7\Psr7RequestFactory;
use GraphQL\Error\DebugFlag;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Schema;
use Nette\DI\Container;

class GraphQL
{
	public function __construct(private readonly Container $container)
	{
	}

	/**
	 * @return array<mixed>
	 * @throws \Nette\Application\BadRequestException
	 */
	public function execute(): array
	{
		try {
			$rawInput = \file_get_contents('php://input');

			if (!$rawInput) {
				throw new \RuntimeException('No input');
			}

			$input = \json_decode($rawInput, true);
			$query = $input['query'];
			$variableValues = $input['variables'] ?? null;
			$operationName = $input['operationName'] ?? null;

			$rootValue = [];

			$result = \GraphQL\GraphQL::executeQuery($this->getSchema(), $query, $rootValue, null, $variableValues, $operationName);
			$output = $result->toArray($this->getDebugFlag());
		} catch (\Throwable $e) {
			if ($this->container->getParameters()['debugMode'] && !$this->container->getParameters()['productionMode']) {
				throw $e;
			}

			$output = [
				'error' => [
					'message' => $e->getMessage(),
				],
			];
		}

		return $output;
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

			$server = new StandardServer([
				'schema' => $this->getSchema(),
				'rootValue' => [],
				'queryBatching' => true,
			]);

			/** @var \GraphQL\Executor\ExecutionResult $result */
			$result = $server->executePsrRequest($psrRequest);

			return $result->toArray($this->getDebugFlag());
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
			$debug = DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE;
		}

		return $debug;
	}
}
