<?php
class Router {
	#[Route('/img/{file_name}')]
	#[Route('/img/{width}/{height}/{file_name}')]
	public static function img($file_name, $width = null, $height = null) {
		global $url;
		$image = null;
		$dest_file = '.' . $url;
		//if the file asked for in the URL isn't present, try to generate it
		if(!file_exists($dest_file)){
			$name = explode('.', $file_name)[0];
			$extension = strtolower(explode('.', $file_name)[1]);
			if($extension == 'jpeg'){
				$extension = 'jpg';
			}
			//convert if needed (i.e if there's a file with same name but different extension)
			$extensions = ['jpg', 'png', 'webp', 'gif', 'ico'];
			$src_file = './img/' . $file_name;
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
								Response::hard404();
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
				if($width && $height && $extension != 'svg'){
					//create the necessary folder structure
					$folder_structure = './img/' . $width . '/' . $height . '/';
					if(!file_exists($folder_structure)){
						mkdir($folder_structure, 0777, true);
					}

					$old_width = getimagesize($src_file)[0];
					$old_height = getimagesize($src_file)[1];
					//dst - src
					$temp_height =  $old_height * $width / $old_width;
					$temp_width =  $old_width * $height / $old_height;
					if($temp_height >= $height){
						$image_p = imagecreatetruecolor($width, $temp_height);
						imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $temp_height,  $old_width, $old_height);
						$image_p = imagecrop($image_p, ['x' => 0, 'y' => ($temp_height - $height) /2, 'width' => $width, 'height' => $height]);
					}else{
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
				Response::hard404();
			}
		}
		$dest_file = substr($dest_file, 1);
		set_time_limit(0);
		do {
		    if (file_exists($dest_file)) {
		        Response::file($dest_file);
		        break;
		    }
		} while(true);
	}

	#[Route('/blog/{slug}')]
	public static function blogSingle($slug) {
		global $config;
		$folder = './blogs/' . $slug . '/';
		$template = './blogs/single.php';
		$data = Data::getDataFromFolder($folder);
		$thread = [
			$config['website-name'] => '/',
			'blog' => '/blog',
			$data['meta']['h1'] => Data::getPostURL('blog', $slug)
		];
		Response::page(new Page(array(
			'folder' => $folder,
			'template' => $template,
			'thread' => $thread,
			'data' => $data
		)));
	}

	#[Route('/blog/')]
	public static function blog() {
		// global $folder;
		// global $template;
		// global $data;
		// global $thread;
		global $config;

		$folder = './blogs/__list/';
		$template = $folder . 'template.php';
		$data = Data::getDataFromFolder($folder);
		$data['list'] = Data::listPosts('blog');
		// var_dump($data['list'][1]);
		$thread = [
			$config['website-name'] => '/',
			'blog' => '/blog'
		];
		Response::page(
			new Page(array(
				'folder' => $folder,
				'template' => $template,
				'thread' => $thread,
				'data' => $data
			))
		);
	}

	#[Route('/tag/{slug}')]
	public static function tag($slug) {
		// global $folder;
		// global $template;
		// global $data;
		// global $thread;
		global $config;

		$folder = './blogs/__tag/';
		$template = $folder . 'template.php';
		$data = Data::getDataFromFolder($folder);
		$data['tag'] = strtolower(str_replace('-', ' ', $slug));
		$thread = [
			$config['website-name'] => '/',
			'tag' => '',
			$data['tag'] => '/tag/' . $slug
		];
		$l = Data::listPosts('blog');
		$data['list'] = [];
		foreach ($l as $blog){
			if(array_key_exists('meta', $blog) && array_key_exists('tags', $blog['meta'])){
				$tags = $blog['meta']['tags'];
				if(in_array($data['tag'], $tags)){
					array_push($data['list'], $blog);
				}
			}
		}
		Response::page(
			new Page(array(
				'folder' => $folder,
				'template' => $template,
				'thread' => $thread,
				'data' => $data
			))
		);
	}

	#[Route('/{slug}')]
	public static function page($slug) {
		$folder = './pages/' . $slug . '/';
		if(!file_exists($folder)){
			Response::soft404();
		}
		$template = $folder . 'template.php';
		if(!file_exists($template)){
			$template = './pages/single.php';
		}
		$data = Data::getDataFromFolder($folder);
		Response::page(
			new Page(array(
				'folder' => $folder,
				'template' => $template,
				'data' => $data
			))
		);
	}

	// #[Route('/{post-type}/{slug}')]
	// public static function postSingle(){
	// 	echo 'postSingle';
	// }
	//
	// #[Route('/{post-type}/')]
	// public static function post(){
	// 	echo 'post';
	// }

	#[Route('/')]
	public static function homepage() {
		global $config;
		$folder = 'pages/' . $config['home-page-folder'] .'/';
		$template = $folder . 'template.php';
		$data = Data::getDataFromFolder($folder);
		Response::page(
			new Page(array(
				'folder' => $folder,
				'template' => $template,
				'data' => $data
			))
		);
	}
}
