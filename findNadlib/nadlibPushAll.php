<?php

$cwd = getcwd();
do {
	$path = fgets(STDIN);
	$path = trim($path);
	echo $path."\n";
	chdir($path);
	run('hg push -f C:\Users\DEPIDSVY\nadlib');
	chdir($cwd);
	echo "\n";
} while (!feof(STDIN));

function run($cmd) {
	echo '> '.$cmd."\n";
	passthru($cmd);
}
