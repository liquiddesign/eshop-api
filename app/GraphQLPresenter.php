<?php

declare(strict_types=1);

namespace App;

use GraphQL\Fields\GraphQLTypeField;
use GraphQL\Schemas\Schema;
use GraphQL\Servers\Server;
use GraphQL\Types\GraphQLObjectType;
use GraphQL\Types\GraphQLString;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Presenter;
use Nette\Http\Request;
use Tracy\Debugger;

class GraphQLPresenter extends Presenter
{
	/** @inject */
	public GraphQL $graphQLite;

	/** @inject */
	public Request $request;

	public function actionDefault(): void
	{
		Debugger::$showBar = false;

		if ($this->request->getMethod() === 'GET') {
			Debugger::$showBar = true;
		}

		$response = new JsonResponse($this->graphQLite->executeServer());

		/** @var \StORM\Bridges\StormTracy<\stdClass> $stormTracy */
		$stormTracy = Debugger::getBar()->getPanel('StORM\Bridges\StormTracy');

		Debugger::log('After response:' . Debugger::timer());
		Debugger::log('Storm total time:' . $stormTracy->getTotalTime());
		Debugger::log('Storm total queries:' . $stormTracy->getTotalQueries());

		$this->sendResponse($response);
	}
}
