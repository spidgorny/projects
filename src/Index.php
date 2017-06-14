<?php

class Index {

	var $parentFolder;

	var $shortName;

	function render() {
		$request = new ArrayObjectSafe($_REQUEST);
		$path = $request->path ?: '../../';

		$p = new ProjectLister($path);
		$p->performAction();

		$this->shortName = basename(realpath($path));

		$this->parentFolder = substr(
			str_replace(
				realpath(__DIR__.'/../../'), '',
				dirname(realpath($path)))
			, 1);
		require __DIR__ . '/../template.phtml';
	}

}
