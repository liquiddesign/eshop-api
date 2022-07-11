<?php

declare(strict_types=1);

namespace App;

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

		$this->sendResponse($response);
	}
}
