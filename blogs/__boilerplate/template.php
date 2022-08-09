<?php if($data['meta']['thumbnail']):?>
	<img src="/img/2000/200/<?php echo $data['meta']['thumbnail']; ?>">
<?php endif;?>

<h1><?php echo $data['meta']['h1']; ?></h1>

<div class="container">
	<?php if($data['meta']['created_at']): ?>
		<span style="display: block;"><b>Created : </b><?php echo date("F j, Y, g:i a", strtotime($data['meta']['created_at'])); ?></span>
	<?php endif; ?>
	<?php if($data['meta']['updated_at']): ?>
		<span style="display: block;"><b>Last modification : </b><?php echo date("F j, Y, g:i a", strtotime($data['meta']['updated_at'])); ?></span>
	<?php endif; ?>
	<?php foreach($data['meta']['tags'] as $t): ?>
		<span class="tag"><a href="/tag/<?php echo slugify($t); ?>"><?php echo $t; ?></a></span>
	<?php endforeach; ?>

	<?php echo $data['content']; ?>
</div>

<!-- the following sections can also be filled through separate files :
CSS and JS imports -> imports.html
JS in tag -> script.js
CSS in tag -> style.css

note that if both the section and its corresponding file contain code, they will both be included in the following order : file content > section content -->

<!-- CSS and JS imports -->
 <?php ob_start(); ?>
 	<script src="https://foobar.com"></script>
 	<link rel="stylesheet" href="https://foobar.com"></link>
 <?php $data['imports'] .= ob_get_contents(); ob_end_clean(); ?>

<!-- JS in tag -->
 <?php ob_start(); ?>
 	<script>
 		//js scripts here
 	</script>
 <?php $data['js_intag'] .= ob_get_contents(); ob_end_clean(); ?>

<!-- CSS in tag (if it's a bit long, add it directly in the style.css file) -->
 <?php ob_start(); ?>
 	<style>
 		/*CSS rules here*/
 	</style>
 <?php $data['css_intag'] .= ob_get_contents(); ob_end_clean(); ?>
