<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">

		<?php if(!empty($data['meta']['meta-description'])): ?>
			<meta name="description" content="<?php echo $data['meta']['meta-description']; ?>">
		<?php endif; ?>

		<link href="favicon.ico" rel="icon" type="image/x-icon"/>

		<?php if(!empty($data['meta']['title'])): ?>
			<title><?php echo $data['meta']['title']; ?></title>
		<?php elseif(!empty($params['website_name'])): ?>
			<title><?php echo $params['website_name']; ?></title>
		<?php endif;?>

		<link rel="stylesheet" href="style.css">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/themes/prism-okaidia.min.css" rel="stylesheet" />


		<?php if(!empty($data['imports'])): ?>
			<?php include($data['imports']); ?>
		<?php endif; ?>
	</head>
	<body>
		<header>
			<?php if(!empty($config['website_name'])): ?>
				<a href="/"><img src="logo.jpg" title="<?php echo $config['website_name']; ?>"></a>
				<p><?php echo $config['website_name']; ?></p>
			<?php endif; ?>
			<nav>
				<?php if(!empty($menu)): ?>
					<ul>
						<?php foreach($menu as $m => &$href):?>
							<li><a href="<?php echo $href; ?>"><?php echo $m; ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</nav>
		</header>

		<?php if(!empty($thread)): ?>
			<nav aria-label="Breadcrumb" class="breadcrumb">
			    <ul>
					<?php foreach($thread as $name => $link):
						if($name != array_key_last($thread)):?>
							<li><a href="<?php echo $link; ?>"><?php echo $name; ?></a></li>
						<?php else :?>
							<li><span aria-current="page"><?php echo $name; ?></span></li>
						<?php endif;
					endforeach;?>
			    </ul>
			</nav>
		<?php endif;?>

		<h2>Test IMG !!!!!!!!!!!!!!!!!!</h2>
		<img src="img/100/100/sample.jpg">
		<img src="img/sample.jpg">
		
		<?php include($template); ?>

		<footer>
			<?php if(!empty($footer_menu)): ?>
				<ul>
					<?php foreach($footer_menu as $m => &$href):?>
						<li><a href="<?php echo $href; ?>"><?php echo $m; ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif;?>
		</footer>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/components/prism-core.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.28.0/plugins/autoloader/prism-autoloader.min.js"></script>
		<?php if(!empty($data['css'])):?>
			<style>
				<?php echo $data['css']; ?>
			</style>
		<?php endif; ?>
		<?php if(!empty($data['css_intag'])){
			echo $data['css_intag'];
		} ?>

		<?php if(!empty($data['js'])):?>
			<script>
				<?php echo $data['js']; ?>
			</script>
		<?php endif; ?>
		<?php if(!empty($data['js_intag'])){
			echo $data['js_intag'];
		} ?>
	</body>
</html>
