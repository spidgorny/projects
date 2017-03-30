<?php

// Windows grep is not working
do {
	$line = fgets(STDIN);
	if (strpos($line, 'nadlib\SQL')) {
		echo str_replace('\SQL', '', $line);
	}
} while (!feof(STDIN));
