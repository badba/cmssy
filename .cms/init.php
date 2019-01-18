<?php

\chdir(__DIR__);

require './include/autoload.php';

\attachAutoLoader('', '../.cms/lib/');
\attachAutoLoader('', '../.cms/include/');
\attachAutoLoader('', '../.custom/lib/');
\attachAutoLoader('', '../.custom/include/');

\set_error_handler(function($severity, $message, $file, $line) {
	if (! (\error_reporting() & $severity)) {
		return;
	}
	throw new \ErrorException($message, 0, $severity, $file, $line);
});

require_once '../.cms/lib/GuzzleHttp/Promise/functions_include.php';
require_once '../.cms/lib/Jasny/array_functions.php';
require_once '../.cms/lib/Jasny/case_functions.php';
require_once '../.cms/lib/Jasny/file_functions.php';
require_once '../.cms/lib/Jasny/func_functions.php';
require_once '../.cms/lib/Jasny/object_functions.php';
require_once '../.cms/lib/Jasny/server_functions.php';
require_once '../.cms/lib/Jasny/string_functions.php';
require_once '../.cms/lib/Jasny/type_functions.php';

if (\file_exists($customInitPath = '../.custom/init.php')) {
	require_once $customInitPath;
}

\Badba\CMSsy\FrontController::run();
