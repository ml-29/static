<?php
class Response {
	public static function file($path){
		header('Content-Type: ' . mime_content_type($path));
		header('Content-Disposition: inline; filename="' . $path . '"');
		readfile($path);//should contain the full path to the file
	}
	public static function page(Page $page){
		$page->render();
		die();
	}
	public static function soft404(){
		$page = new Page(array('template' => '404.php'));
		$page->render();
		die();
	}
	public static function hard404(){
		http_response_code(404);
		die();
	}
}
