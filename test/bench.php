<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/CSSJanusBenchmark.php';

$fixtures = null;
if (isset($argv[1])) {
	$name = basename($argv[1]);
	$data = file_get_contents($argv[1]);
	$fixtures = [ $name => $data ];
}

( new CSSJanusBenchmark($fixtures) )->run();
