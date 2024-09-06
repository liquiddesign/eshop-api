<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

\LqGrAphi\Handlers\IndexHandler::handle(\src\Bootstrap::boot()->createContainer());
