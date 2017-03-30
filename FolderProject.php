<?php

class FolderProject {

	var $path;

	function __construct($path) {
		$this->path = $path;
	}

	function render() {
		ob_start();
		require __DIR__ . '/FolderProject.phtml';
		$content = ob_get_clean();
		return $content;
	}

	function isProject($path = NULL) {
		return is_dir(($path ?: $this->path).'/.idea');
	}

	function hasFiles($path) {
		$all = scandir($path);
		return array_reduce($all, function ($acc, $el) use ($path) {
			return $acc || is_file($path . '/' . $el);
		}, false);
	}

	function countFiles($path) {
		$all = scandir($path);
		return array_reduce($all, function ($acc, $el) use ($path) {
			return $acc + is_file($path . '/' . $el);
		}, 0);
	}

	function showSubs() {
		$content = [];
		if (is_dir($this->path)) {
			$pl = new ProjectLister($this->path);
			$folders = $pl->getFolders();
			foreach ($folders as $i => $path) {
				$isProject = $this->isProject($this->path.'/'.$path);
				//debug($this->path.'/'.$path, $isProject);
				if ($isProject) {
					$hasFiles = $this->hasFiles($this->path.'/'.$path);
					$countFiles = $this->countFiles($this->path.'/'.$path);
					if (!$hasFiles || $countFiles < 5) {
						$link = '?path='.urlencode($this->path . '/' . $path);
						$content[] = '<li><a href="'.$link.'">' . $path . '</a></li>';
					}
				}
			}
		}
		return $content;
	}

	function showProjectInfo() {
		//$content[] = 'Project path: '.$this->path.BR;

		if (is_file($this->path.'/VERSION.json')) {
			$content[] = $this->showVersionInfo();
		} else {
			$content[] = $this->getRepoIcon();
		}
		return $content;
	}

	function getRepoIcon($path = NULL) {
		$path = $path ?: $this->path;
		$content = [];

		$isGit = is_dir($path.'/.git');
		if ($isGit) {
			$content[] = '<i class="fa fa-github" aria-hidden="true"
			title="'. htmlspecialchars($path).'"></i>';
		}

		$isHG = is_dir($path.'/.hg');
		if ($isHG) {
			$content[] = '<i class="fa fa-bitbucket" aria-hidden="true"
			title="'. htmlspecialchars($path).'"></i>';
		}

		if (!$content) {
			$content[] = '<i class="fa fa-sitemap" aria-hidden="true" 
			title="'. htmlspecialchars($path).'"></i>';
		}

		return $content;
	}

	function showVersionInfo() {
		$content = [];
		$data = json_decode(file_get_contents($this->path.'/VERSION.json'));
		foreach ($data->repos as $repo) {
			$rPath = $this->path.'/'.$repo->path;
			$content[] = [
				$this->getRepoIcon($rPath), ' ',
				basename($repo->path).' ['.$repo->nr.']', BR];
		}
		return $content;
	}

}
