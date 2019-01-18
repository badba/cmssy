<?php

function attachAutoLoader($prefix, $baseDir) {
	\spl_autoload_register(function($clazz) use ($prefix, $baseDir) {
		$len = \strlen($prefix);
		if (\strncmp($prefix, $clazz, $len) === 0) {
			if (\file_exists($scriptPath = $baseDir . \str_replace('\\', '/', \substr($clazz, $len)) . '.php')) {
				require($scriptPath);
			}
		}
	});
}
