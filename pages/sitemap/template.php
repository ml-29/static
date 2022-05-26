<div class="accent">
	<h1><?php echo $data['meta']['h1']; ?></h1>
</div>

<div class="container">
	<?php $types = listPostTypes();
	foreach($types as $t): ?>
		<h2><?php echo ucfirst($t); ?></h2>
		<ul>
			<?php $t = substr($t, 0, -1);
			$posts = listPosts($t);
			foreach($posts as $p): ?>
				<li><a href="<?php echo $p['href']; ?>"><?php echo $p['meta']['h1']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endforeach; ?>
</div>
