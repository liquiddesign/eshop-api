<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

\LqGrAphi\Handlers\IndexHandler::handle(\EshopApi\Bootstrap::boot()->createContainer());
