<?php

class FolderScanner {

	var $path;

	function __construct($path) {
		$this->path = $path;
	}

	function getFiles() {
		return scandir($this->path);
	}

	function getFolders() {
		$all = $this->getFiles();
		return array_filter($all, function ($el) {
			return is_dir($el);
		});
	}

	function hasProjects() {
		$folders = $this->getFolders();
		foreach ($folders as $path) {
			$fp = new FolderProject($path);
			if ($fp->isProject()) {
				return true;
			}
		}
		return false;
	}

}
