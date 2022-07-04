<img src="/img/2000/200/background.jpg">

<h1><?php echo $data['meta']['h1']; ?></h1>



<div class="container">
	<?php echo $data['content']; ?>

	<?php if(!empty($list)): ?>
		<?php foreach($list as $l):?>
			<div class="line">
				<a href="<?php echo $l['href']; ?>">
					<img src="/img/200/100/<?php echo $l['meta']['thumbnail']; ?>" alt="<?php echo $l['meta']['title']; ?>">
				</a>
				<div>
					<h2><a href="<?php echo $l['href']; ?>"><?php echo $l['meta']['h1']; ?></a></h2>
					<?php echo $l['excerpt']; ?>
					<div style="margin-bottom: 10px;">
						<?php foreach($l['meta']['tags'] as $t): ?>
							<span class="tag"><a href="/tag/<?php echo slugify($t); ?>"><?php echo $t; ?></a></span>
						<?php endforeach; ?>
					</div>
					<a class="button" href="<?php echo $l['href']; ?>">Learn more >>></a>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>
</div>


<?php ob_start(); ?>
	<!-- blog list imports -->
	<!-- <script src="imports"></script> -->
<?php $data['imports'] .= ob_get_contents(); ob_end_clean(); ?>

<?php ob_start(); ?>
	<!-- <script>
		//js scripts here
	</script> -->
<?php $data['js_intag'] .= ob_get_contents(); ob_end_clean(); ?>

<?php ob_start(); ?>
	<style>
		h2 {
			margin-top: 0;
		}
	</style>
<?php $data['css_intag'] .= ob_get_contents(); ob_end_clean(); ?>
