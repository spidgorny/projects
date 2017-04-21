<?php

define('BR', '<br />'.PHP_EOL);
session_start();

function output($content) {
	if (is_array($content)) {
		$list = '';
		array_walk_recursive($content, function ($el, $i) use (&$list) {
			//echo htmlspecialchars($el), BR;
			$list .= $el . PHP_EOL;
		});
		return $list;
	} else {
		return $content . PHP_EOL;
	}
}

function debug($a) {
	echo '<pre>';
	var_dump(func_num_args() == 1 ? $a : func_get_args());
	echo '</pre>';
}

require_once __DIR__ . '/src/ArrayObjectSafe.php';
$request = new ArrayObjectSafe($_REQUEST);
$path = $request->path ?: '.';

require_once __DIR__ . '/src/ProjectLister.php';
$p = new ProjectLister($path);
$p->performAction();
require __DIR__.'/template.phtml';
