<?php
class Page {
	private $folder = '';
	private $menu = [
		'Home' => '/',
		'Blog' => '/blog/',
		'Contact us' => '/contact-us'
	];
	private $thread = [];
	private $footer_menu = [
		'Home' => '/',
		'About' => '/about',
		'Site map' =>'/sitemap'
	];

	private $template = '';
	private $data = [];

	public function __construct($params = array()){
	    foreach ($params as $param=>$param_value) {
			if(property_exists($this, $param)){
				$this->$param = $param_value;
			}
	    }
	}

	public function render(){
		global $config;
		global $url;
		foreach(get_object_vars($this) as $name=>$value){
			global ${$name};
			${$name} = $this->$name;
		}
		include('base.php');
	}
}
