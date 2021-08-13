<?php

function callMethod($object, string $method , array $parameters = [])
{
	try {
		$className = get_class($object);
		$reflection = new \ReflectionClass($className);
	} catch (\ReflectionException $e) {
		throw new \Exception($e->getMessage());
	}

	$method = $reflection->getMethod($method);
	$method->setAccessible(true);

	return $method->invokeArgs($object, $parameters);
}

/**
 * Define the content of the standard input clone for tests
 * @param array $lines lines of the files
 * @return void
 */
function defineStdinClone(array $lines): void
{
	if (isset($GLOBALS['testSTDIN'])) {
		$fakeStdinHandle = $GLOBALS['testSTDIN'];
		fclose($fakeStdinHandle);
	}
	file_put_contents(UserInputTest::stdinClone, implode("\n", $lines));
	$GLOBALS['testSTDIN'] = fopen(UserInputTest::stdinClone, 'r');
}