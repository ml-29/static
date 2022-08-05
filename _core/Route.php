<?php
class Route {
	private string $pattern;

	public function __construct(string $pattern){
		$this->pattern = $pattern;
	}

	//checks if the provided url and the pattern match each other
	public function matches($url){
		//get components from url that has to be checked
		$url_cmp = self::parseURL($url);
		//get route components (what is expected)
		$pattern_cmp = array_values(array_filter(explode('/', $this->pattern)));

		if(count($url_cmp) != count($pattern_cmp)){
			return false;//consider not matching if lengths are different
		}

		foreach($pattern_cmp as $index=>$pattern_el){//check that each component matches
			if(!self::isParam($pattern_el) && $url_cmp[$index] != $pattern_el){//element doesn't match the expected fixed value exactly
				return false;
			}
		}
		return true;//if no mismatch were spotted, consider matching
	}

	//extracts the param values from the URL so they can be used as a functions' array of parameters
	public function getParamValuesFromURL($url){
		$cmp = array_values(array_filter(explode('/', $this->pattern)));

		$url = self::parseUrl($url);
		$r = array();
		foreach($cmp as $index=>$c){
			if(self::isParam($c)){
				$r[self::getParamName($c)] = $url[$index];
			}
		}
		return $r;
	}

	//gets the name of a route parameter inside the pattern (e.g. {param} becomes param)
	private static function getParamName($cmp){
		return substr($cmp, 1, -1);
	}
	//checks that a pattern component (cmp) is a parameter (e.g. is written between brackets such as {cmp})
	private static function isParam($cmp){
		return substr($cmp, 0, 1) == '{' && substr($cmp, -1, 1) == '}' && preg_match('/^[a-zA-Z_]+/', self::getParamName($cmp));
	}
	private static function parseURL($url){
		$r = parse_url($url);
		$r = array_values(array_filter(explode('/', $r['path'])));
		return $r;
	}
}
