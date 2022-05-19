<h1><?php echo $data['meta']['h1']; ?></h1>

<?php echo $data['content']; ?>

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
