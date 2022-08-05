<?php
require_once('libs/Parsedown.php');
$Parsedown = new Parsedown();

class Data {
	//extracts data from a folder (page, blog or any other post)
	public static function getDataFromFolder($folder){
		global $Parsedown;
		$data = [];

		$data['content'] = '';
		$data['excerpt'] = '';
		$data['meta'] = [];

		if(!array_key_exists('imports', $data)){
			$data['imports'] = '';
		}
		if(!array_key_exists('css', $data)){
			$data['css'] = '';
		}
		if(!array_key_exists('js', $data)){
			$data['js'] = '';
		}
		if(file_exists($folder . 'content.md')){
			$data['content'] = $Parsedown->text(file_get_contents($folder . 'content.md'));
			$data['excerpt'] = self::excerptFromText($data['content']);
		}
		if(file_exists($folder . 'meta.yml')){
			$data['meta'] = yaml_parse_file($folder . 'meta.yml');
		}
		if(file_exists($folder . 'imports.html')){
			$data['imports'] .= file_get_contents($folder . 'imports.html');
		}
		if(file_exists($folder . 'style.css')){
			$data['css'] .= file_get_contents($folder . 'style.css');
		}
		if(file_exists($folder . 'scripts.js')){
			$data['js'] .= file_get_contents($folder . 'scripts.js');
		}
		return $data;
	}

	public static function listPosts($type){
		global $config;
		$s = scandir($type . 's');
		$r = [];
		foreach($s as $v){
			if(!is_dir($v) && (isSlug($v) || ($type == 'page' && $v == $config['home-page-folder']))) {
				$data = self::getPostData($type, $v);
				array_push($r, $data);
			}
		}
		return $r;
	}
	public static function listPostTypes(){
		$s = scandir(getcwd());
		$r = [];
		foreach($s as $v){
			if(is_dir($v) && isSlug($v) && $v != 'img') {
				array_push($r, $v);
			}
		}
		return $r;
	}

	public static function getPostFolder($type, $slug){
		return $type . 's/' . $slug . '/';
	}

	public static function getPostURL($type, $slug){
		if($type == 'page'){
			return '/' . $slug;
		}
		return '/' . $type . '/' . $slug . '/';
	}
	public static function getPostData($type, $slug){
		return array_merge(
			self::getDataFromFolder(self::getPostFolder($type, $slug)),
			['href' => self::getPostURL($type, $slug)]
		);
	}

	public static function getTemplate($theme, $post_type, $list_or_single){
		$template = '';
		global $data;
		global $folder;
		if(array_key_exists('meta', $data) && array_key_exists('template', $data['meta'])){
			$meta_template = $data['meta']['template'];
		}else{
			$meta_template = false;
		}

		if(file_exists($template = $folder . 'template.php')){
			return $template;
		}else if($meta_template && file_exists($template = $folder . $meta_template)){
			return $template;
		}else if(file_exists($template = $folder . $list_or_single . '.php')){
			return $template;
		}
		return 'themes/' . $theme . '/' . $post_type . '-' . $list_or_single . '.php';
	}
	private static function excerptFromText($text, $limit=55) {
		if(str_word_count($text, 0) > $limit) {
			$words = str_word_count($text, 2);
			$pos   = array_keys($words);
			$text  = substr($text, 0, $pos[$limit]) . '...';
		}
		return $text;
	}
}
