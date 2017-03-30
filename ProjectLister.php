<?php

require_once __DIR__ . '/ArrayObjectSafe.php';
require_once __DIR__ . '/FolderProject.php';

class ProjectLister {

	var $path;

	var $request;

	function __construct($path = '.') {
		$this->path = $path;
		$this->request = new ArrayObjectSafe($_REQUEST);
	}

	function getFolders() {
		$files = scandir($this->path);
		$folders = $this->filterFiles($files);
		return $folders;
	}

	function isProject($path = NULL) {
		$path = $path ?: $this->path;
		return is_dir($path)
			&& is_dir($path.'/.idea');
	}

	function render() {
		$content = [];
		$content[] = $this->showPinned();
		if ($this->isProject()) {
			$project = new FolderProject($this->path);
			$content[] = $project->render();
			$content[] = $this->showSubProjects();
		} else {
			$content[] = $this->showSubProjects();
		}
		return $content;
	}

	function showSubProjects() {
		$content = [];
		$files = $this->getFolders();
		foreach ($files as $file) {
			//$isProject = $this->isProject($this->path.'/'.$file);
			$isProject = is_dir($this->path.'/'.$file);
			if ($isProject) {
				$project = new FolderProject($this->path . '/' . $file);
				$content[] = $project->render();
			}
		}
		$content = ['<ul class="bare">', $content, '</ul>'];
		return $content;
	}

	function filterFiles(array $files) {
		return array_filter($files, function ($file) {
			$dot = $file[0] == '.';
			$file = is_file($file);
			return !$dot && !$file;
		});
	}

	function performAction() {
		$method = $this->request->action.'Action';
		if (method_exists($this, $method)) {
			echo $this->$method();
		}
	}

	function pinAction() {
		$_SESSION['pin'][] = $this->path;
		$_SESSION['pin'] = array_unique($_SESSION['pin']);
	}

	function showPinned() {
		$content = [];
		foreach ($_SESSION['pin'] as $pin) {
			$p = new FolderProject($pin);
			$content[] = $p->render();
		}
		if ($content) {
			$content = ['<ul class="bare pinned">', $content, '</ul>'];
			$content[] = '<hr />';
		}
		return $content;
	}

	function unpinAction() {
		$key = array_search($this->path, $_SESSION['pin']);
		//debug($_SESSION['pin'], $key);
		array_splice($_SESSION['pin'], $key, 1);
	}

}
