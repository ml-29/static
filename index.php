<?php

require_once('Parsedown.php');
$Parsedown = new Parsedown();

$config = yaml_parse_file('config.yml');

$parsed_url = [];

$menu = [
	'Home' => '/',
	'Blog' => '/blog'
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

function isSlug($s){
	return preg_match('/^[a-z0-9]+(-?[a-z0-9]+)*$/i', $s);
}

function getData($folder){
	global $Parsedown;
	if(file_exists($folder . 'content.md')){
		$data['content'] = $Parsedown->text(file_get_contents($folder . 'content.md'));
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

function route($url){
	global $folder;
	global $template;
	global $data;
	global $thread;
	global $parsed_url;

	$parsed_url = parseURL($url);
	$image = null;

	//if request for an image
	if(sizeof($parsed_url['path']) >= 1 && $parsed_url['path'][0] == 'img'){
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
			//svg files are never converted as they are already the best option, that is lighter thus better suited for fast loading than other formats
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

			//resize if needed (needed if size parameters are present and file is not an svg)
			if(sizeof($parsed_url['path']) == 4 && $extension != 'svg'){
				//create the necessary folder structure
				$folder_structure = substr($dest_file, 0, strrpos($dest_file, '/') + 1);
				if(!file_exists($folder_structure)){
					mkdir($folder_structure, 0777, true);
				}

				$width = $parsed_url['path'][1];
				$height = $parsed_url['path'][2];

				$image = imagescale($image , $width , $height);
			}
			if($image){//if a file has been generated at all, save it
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
			$template = 'blogs/__single/single.php';
			$data = getData($folder);
			$thread = [
				$config['website-name'] => '/',
				'blog' => '/blog',
				$data['meta']['h1'] => getPostURL('blog', $parsed_url['path'][1])
			];
			//TODO:get tags from meta.yml + display the on the template
		}elseif(sizeof($parsed_url['path']) == 1 && $parsed_url['path'][0] == 'blog'){//blog list /blog
			$folder = 'blogs/__list/';
			$template = $folder . 'list.php';
			$data = getData($folder);
			$list = listPosts('blog');
			$thread = [
				$config['website-name'] => '/',
				'blog' => '/blog'
			];
		}else if(sizeof($parsed_url['path']) == 1 && isSlug($parsed_url['path'][0])){//page / + slug
			$folder = 'pages/' . $parsed_url['path'][0] . '/';
			$template = $folder . '/__single/single.php';
			$data = getData($folder);
			$thread = [
				$config['website-name'] => '/',
				$data['meta']['h1'] => getPostURL('page', $parsed_url['path'][0])
			];
		}else if(sizeof($parsed_url['path']) == 0){//home : /
			$folder = 'pages/home_page/';
			$template = $folder . 'main.php';
			$data = getData($folder);
		}
		include('base.php');
	}
}

// route('https://localhost:80/img/200/200/vector.svg');
// route('https://localhost:80/img/vector.svg');
//
// route('https://localhost:80/img/200/200/sample.png');
// route('https://localhost:80/img/200/200/sample.jpg');
// route('https://localhost:80/img/200/200/sample.gif');
// route('https://localhost:80/img/200/200/sample.webp');
//
//
// route('https://localhost:80/img/sample.PNG');
// route('https://localhost:80/img/100/100/sample.jpg');
// route('https://localhost:80/img/sample.gif');

// route('https://localhost:80/img/sample.boop');
// route('https://localhost:80/img/boop.boop');
// route('https://localhost:80/img/boop.webp');
// route('https://localhost:80/img/favicon.ico');
route($_SERVER['REQUEST_URI']);
