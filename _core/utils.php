<?php
function slugify($s){
	return str_replace(' ', '-', strtolower($s));
}
function isSlug($s){
	return preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $s);
}
