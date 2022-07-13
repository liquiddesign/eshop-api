<?php

declare(strict_types=1);

//use Spatie\TypeScriptTransformer\Collectors\Collector;
//use Spatie\TypeScriptTransformer\TypeScriptTransformer;
//use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
//use Spatie\TypeScriptTransformer\Structures\TransformedType;

require __DIR__ . '/vendor/autoload.php';

// to maintanace on rename .maintenance.php to maintenance.php
if (\is_file($maintenance = __DIR__ . '/maintenance.php')) {
	require $maintenance;
}

//class EnumCollector extends Collector
//{
//	public function getTransformedType(ReflectionClass $class): ?TransformedType
//	{
//		$transformer = new Spatie\TypeScriptTransformer\Transformers\DtoTransformer(new TypeScriptTransformerConfig());
//
//		if (\Nette\Utils\Strings::contains($class->getName(), 'Repository')){
//			return null;
//		};
//
//		return $transformer->transform($class, \substr($class->getName(), \strrpos($class->getName(), '\\') + 1));
//	}
//}
//
//$config = TypeScriptTransformerConfig::create()
//	// path where your PHP classes are
//	->autoDiscoverTypes(__DIR__ . '/vendor/liquiddesign/eshop/src/DB')
//	// list of transformers
//	->collectors([EnumCollector::class])
//	// file where TypeScript type definitions will be written
//	->outputFile(__DIR__ . '/js/generated.d.ts');
//
//TypeScriptTransformer::create($config)->transform();

\App\Bootstrap::boot()
	->createContainer()
	->getByType(\App\Application::class)
	->run();
