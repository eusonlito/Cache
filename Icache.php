<?php
namespace Cache;

interface Icache {
    public function exists ($key);
    public function set ($key, $value, $expire);
    public function get ($key);
    public function delete ($key);
    public function clear ();
    public function expire ($key);
}
