<?php if($data['meta']['thumbnail']):?>
	<img src="/img/2000/200/<?php echo $data['meta']['thumbnail']; ?>">
<?php endif;?>

<!-- TODO : add dates (updated, edited + tags) -->


<div class="h1-overlay">
	<h1><?php echo $data['meta']['h1']; ?></h1>
</div>

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

 <?php ob_start(); ?>
 	<!-- blog list imports -->
 	<script>
		window.alert('hello from single');
	</script>
 <?php $data['imports'] .= ob_get_contents(); ob_end_clean(); ?>

 <?php ob_start(); ?>
 	<script>
 		//js scripts here
 	</script>
 <?php $data['js_intag'] .= ob_get_contents(); ob_end_clean(); ?>

 <?php ob_start(); ?>
 	<style>
 		/*style*/
 	</style>
 <?php $data['css_intag'] .= ob_get_contents(); ob_end_clean(); ?>
