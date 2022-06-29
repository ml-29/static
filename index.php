<?php

require_once('Parsedown.php');
$Parsedown = new Parsedown();

$config = yaml_parse_file('config.yml');

$parsed_url = [];

$menu = [
	'Home' => '/',
	'Blog' => '/blog/'
];

$thread = [];
$footer_menu = [
	'Home' => '/',
	'About' => '/about',
	'Site map' =>'/sitemap'
];

$folder = '';
$template = '';
$data = [];

function parseURL($url){
	$r = parse_url($url);
	$r['path'] = array_values(array_filter(explode('/', $r['path'])));
	return $r;
}

function getExcerpt($text, $limit=55) {
    if (str_word_count($text, 0) > $limit) {
        $words = str_word_count($text, 2);
        $pos   = array_keys($words);
        $text  = substr($text, 0, $pos[$limit]) . '...';
    }
    return $text;
}

function listPosts($type){
	global $config;
	$s = scandir($type . 's');
	$r = [];
	foreach($s as $v){
		if(!is_dir($v) && (isSlug($v) || ($type == 'page' && $v == $config['home-page-folder']))) {
			$data = getPostData($type, $v);
			array_push($r, $data);
		}
	}
	return $r;
}
function listPostTypes(){
	// global $config;
	$s = scandir(getcwd());
	$r = [];
	foreach($s as $v){
		if(is_dir($v) && isSlug($v) && $v != 'img') {
			array_push($r, $v);
		}
	}
	return $r;
}

function isSlug($s){
	return preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $s);
}

function getData($folder){
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
		$data['excerpt'] = getExcerpt($data['content']);
	}
	if(file_exists($folder . 'meta.yml')){
		$data['meta'] = yaml_parse_file($folder . 'meta.yml');
	}
	if(file_exists($folder . 'imports.html')){
		$data['imports'] .= file_get_contents($folder . 'imports.html');
	}
	if(file_exists($folder . 'css.html')){
		$data['css'] .= file_get_contents($folder . 'style.css');
	}
	if(file_exists($folder . 'scripts.js')){
		$data['js'] .= file_get_contents($folder . 'scripts.js');
	}
	return $data;
}

function getPostFolder($type, $slug){
	return $type . 's/' . $slug . '/';
}

function getPostURL($type, $slug){
	if($type == 'page'){
		return '/' . $slug;
	}
	return '/' . $type . '/' . $slug . '/';
}

function getPostData($type, $slug){
	return array_merge(
		getData(getPostFolder($type, $slug)),
		['href' => getPostURL($type, $slug)]
	);
}

function error($code){
	http_response_code($code);
	include($code . '.php'); // provide your own HTML for the error page
	die();
}

function getTemplate($theme, $post_type, $list_or_single){
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

function route($url){
	global $config;
	global $folder;
	global $template;
	global $data;
	global $menu;
	global $footer_menu;
	global $thread;
	global $parsed_url;

	$parsed_url = parseURL($url);
	if(sizeof($parsed_url['path']) >= 1 && $parsed_url['path'][0] == 'static'){
		array_shift($parsed_url['path']);
	}

	//if request for an image
	if(sizeof($parsed_url['path']) >= 1 && $parsed_url['path'][0] == 'img'){
		$image = null;
		$dest_file = './' . implode('/', $parsed_url['path']);

		//if the file asked for in the URL isn't present, try to generate it
		if(!file_exists($dest_file)){
			//extract params from the url
			$full_name = end($parsed_url['path']);
			$name = explode('.', $full_name)[0];
			$extension = strtolower(explode('.', $full_name)[1]);
			if($extension == 'jpeg'){
				$extension = 'jpg';
			}
			//convert if needed (i.e if there's a file with same name but different extension)
			$extensions = ['jpg', 'png', 'webp', 'gif', 'ico'];
			$src_file = './img/' . $full_name;
			//if a corresponding source file doesn't exist at the root of the img directory, try and find an image with same name but different extension to convert from
			//svg files are never converted as they are already the most efficient option resizing and loading-wise
			if(!file_exists($src_file) && in_array($extension, $extensions)){
				foreach ($extensions as $e) {
					$src_file = './img/' . $name . '.' . $e;
					if(file_exists($src_file)){
						switch($e){
							case 'jpg':
								$image = imagecreatefromjpeg($src_file);
								break;
							case 'png':
								$image = imagecreatefrompng($src_file);
								break;
							case 'gif':
								$image = imagecreatefromgif($src_file);
								break;
							case 'webp':
								$image = imagecreatefromwebp($src_file);
								break;
							default :
								error(404);
						}
						break;
					}
					$dest_file = $src_file;
				}
			}else if(file_exists($src_file)){//if source file already exists
				if($extension == 'svg'){//if it's an svg, send it as is
					$dest_file = $src_file;
				}else{//if it's not and svg, load it to use it for the next steps
					$image = imagecreatefromstring(file_get_contents($src_file));
				}
			}

			if($image){//if a file has been generated at all, resize if necessary and save it
				//resize if needed (needed if size parameters are present and file is not an svg)
				if(sizeof($parsed_url['path']) == 4 && $extension != 'svg'){
					//create the necessary folder structure
					$folder_structure = substr($dest_file, 0, strrpos($dest_file, '/') + 1);
					if(!file_exists($folder_structure)){
						mkdir($folder_structure, 0777, true);
					}

					$width = $parsed_url['path'][1];
					$height = $parsed_url['path'][2];

					$old_width = getimagesize($src_file)[0];
					$old_height = getimagesize($src_file)[1];
					//dst - src
					if($width > $height){
						$temp_height =  $old_height * $width / $old_width;
						$image_p = imagecreatetruecolor($width, $temp_height);
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $temp_height,  $old_width, $old_height);
						$image_p = imagecrop($image_p, ['x' => 0, 'y' => ($temp_height - $height) /2, 'width' => $width, 'height' => $height]);
					}else{
						$temp_width =  $old_width * $height / $old_height;
						$image_p = imagecreatetruecolor($temp_width, $height);
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $temp_width, $height,  $old_width, $old_height);
						$image_p = imagecrop($image_p, ['x' => ($temp_width - $width) /2, 'y' => 0, 'width' => $width, 'height' => $height]);
					}
					$image = $image_p;
				}
				switch($extension){
					case 'jpg':
						imagejpeg($image, $dest_file);
						break;
					case 'png':
						imagepng($image, $dest_file);
						break;
					case 'gif':
						imagegif($image, $dest_file);
						break;
					case 'webp':
						imagewebp($image, $dest_file);
						break;
				}
			}else{
				error(404);
			}

		}
		//send the file
		header('Content-Type: ' . mime_content_type($dest_file));
		readfile($dest_file);
	}else{
		if(sizeof($parsed_url['path']) == 2 && $parsed_url['path'][0] == 'blog' && isSlug($parsed_url['path'][1])){//single blog /blog/ + slug
			$folder = 'blogs/' . $parsed_url['path'][1] . '/';
			$template = 'blogs/single.php';
			$data = getData($folder);
			// $template = getTemplate($config['theme'], 'blog', 'single', $folder, $meta['template']);
			// $template = getTemplate($config['theme'], 'blog', 'single');
			$thread = [
				$config['website-name'] => '/',
				'blog' => '/blog',
				$data['meta']['h1'] => getPostURL('blog', $parsed_url['path'][1])
			];
			//TODO:get tags from meta.yml + display the on the template
		}elseif(sizeof($parsed_url['path']) == 1 && $parsed_url['path'][0] == 'blog'){//blog list /blog
			$folder = 'blogs/__list/';
			$template = $folder . 'template.php';
			$data = getData($folder);
			// $template = getTemplate($config['theme'], 'blog', 'list', $folder, $meta['template']);
			// $template = getTemplate($config['theme'], 'blog', 'list');
			$list = listPosts('blog');
			$thread = [
				$config['website-name'] => '/',
				'blog' => '/blog'
			];
		}else if(sizeof($parsed_url['path']) == 1 && isSlug($parsed_url['path'][0])){//page / + slug
			$folder = 'pages/' . $parsed_url['path'][0] . '/';
			$template = $folder . 'template.php';
			if(!file_exists($template)){
				$template = 'pages/single.php';
			}
			$data = getData($folder);
			// $template = getTemplate($config['theme'], 'page', 'single', $folder, $data['meta']['template']);
			// $template = getTemplate($config['theme'], 'page', 'single');
			$thread = [
				$config['website-name'] => '/',
				$data['meta']['h1'] => getPostURL('page', $parsed_url['path'][0])
			];
		}else if(sizeof($parsed_url['path']) == 0){//home : /
			$folder = 'pages/' . $config['home-page-folder'] .'/';
			$template = $folder . 'template.php';
			$data = getData($folder);
			// $template = getTemplate($config['theme'], 'page', 'single');
		}
		if($template){
			include('base.php');
		}else{
			error(404);
		}
	}
}

route($_SERVER['REQUEST_URI']);
// route('/img/150/120/sample.jpg');
// route('/search');

//tests

//blog

//page
