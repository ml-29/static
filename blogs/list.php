<h1><?php echo $data['meta']['h1']; ?></h1>

<?php echo $data['content']; ?>

<?php if(!empty($list)): ?>
	<ul>
		<?php foreach($list as $l):?>
			<li><a href="<?php echo $l['href']; ?>"><?php echo $l['meta']['h1']; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php ob_start(); ?>
	<!-- blog list imports -->
	<script src="imports"></script>
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
