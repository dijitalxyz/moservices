<?php
abstract class Session {
	abstract public function exists($key);
	abstract public function get($key);
	abstract public function set($key, $value, $ttl = NULL);
	abstract public function delete($key);
	abstract public function clear();
}
?>