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

function autoload($class) {
	require_once __DIR__ . '/src/'.$class.'.php';
}
spl_autoload_register('autoload');

class Index {

	var $parentFolder;

	var $shortName;

	function render() {
		$request = new ArrayObjectSafe($_REQUEST);
		$path = $request->path ?: '.';

		$p = new ProjectLister($path);
		$p->performAction();

		$this->shortName = basename(realpath($path));

		$this->parentFolder = substr(
			str_replace(
				realpath(__DIR__.'/../../'), '',
				dirname(realpath($path)))
			, 1);
		require __DIR__ . '/template.phtml';
	}

}

(new Index())->render();
