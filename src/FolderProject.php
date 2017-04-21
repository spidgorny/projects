<?php

class FolderProject {

	var $path;

	function __construct($path) {
		$this->path = $path;
	}

	function render() {
		ob_start();
		$links = '';
		$panelClass = 'info';
		require __DIR__ . '/FolderProject.phtml';
		$content = ob_get_clean();
		return $content;
	}

	function renderDetails() {
		ob_start();
		$links = '<a href="'. ($this->path) .'" target="'. basename($this->path) .'">
				<i class="fa fa-external-link" aria-hidden="true"></i>
			</a>
			<a href="#" onclick="return copy(this);">
				<i class="fa fa-clipboard" aria-hidden="true"></i>
				<input value="'.realpath(__DIR__ . '/../NetBeansProjects/' .$this->path).'" style="display: none;" title="Windows path"/>
			</a>';
		$panelClass = 'success';
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

	function hasProjects() {
		//debug($this->path);
		$children = new FolderScanner($this->path);
		return $children->hasProjects();
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
			exec('cd '.$path.'&& git rev-list --count HEAD', $lines);
			$id = $lines[0];
			exec('cd '.$path.'&& git rev-parse HEAD', $hash);
			$hash = substr($hash[0], 0, 12);
			$content[] = '<span class="label label-default code">'.
				$hash . '<span class="badge">'.$id.'</span>
			</span>';
		}

		$isHG = is_dir($path.'/.hg');
		if ($isHG) {
			$content[] = '<i class="fa fa-bitbucket" aria-hidden="true"
			title="'. htmlspecialchars($path).'"></i>';
			exec('cd '.$path.'&& hg id -ni', $output);
			list($hash, $id) = explode(' ', $output[0]);
			$content[] = '<span class="label label-default code">'.
				$hash . '<span class="badge">'.$id.'</span>
			</span>';
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

	function isPinned() {
		return in_array($this->path, $_SESSION['pin']);
	}

	public function getNameLink() {
		return '<a href="?path='.$this->path.'">'.$this->path.'</a>';
	}

	function showIcon() {
		$content = [];
		$images = glob($this->path.'/*.{jpg,png,gif,ico}', GLOB_BRACE);
		foreach ($images as $path) {
			$content[] = '<img src="'.$path.'" width="32" height="32" style="background: white;"/> ';
			break;
		}
		return output($content);
	}

}
