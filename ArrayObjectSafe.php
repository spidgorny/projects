<?php

class ArrayObjectSafe extends ArrayObject {

	function __get($name) {
		if (isset($this[$name])) {
			return $this[$name];
		} else {
			return NULL;
		}
	}

}
