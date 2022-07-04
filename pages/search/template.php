<?php
	$search = strtolower(trim($_GET['q']));
	$search_terms = explode(' ', $search);
	$blog_posts = listPosts('blog');

	$results = [];

	foreach($blog_posts as $p){
	   if(str_contains($p['meta']['h1'], $search)){//Full-sentence matches in post title
		   //push
		   array_push($results, $p);
	   }else{
		   $all_search_terms = true;
		   foreach($search_terms as $t){//check that All search terms in post title
			   if(!str_contains($p['meta']['h1'], $search)){
				   $all_search_terms = false;
				   break;
			   }
		   }
		   if($all_search_terms){//if all search terms can be found in post title
			   //push
			   array_push($results, $p);
		   }else{
			   $any_search_term = false;
			   foreach($search_terms as $t){//Any search term in post titles.
				   if(str_contains($p['meta']['h1'], $search)){
					   $any_search_term = true;
				   }
			   }
			   if($any_search_term){
				   //push
				   array_push($results, $p);
			   }elseif(str_contains($p['content'], $search)){//Full-sentence matches in post content.
				   //push
				   array_push($results, $p);
			   }elseif(in_array($search, $p['meta']['tags'])){//search full search string in tags
				   //push
				   array_push($results, $p);
			   }
		   }
	   }
	}
?>
<div class="accent">
	<h1>Results for "<?php echo $search; ?>"</h1>
</div>
<div class="container">
	<?php if(!empty($results)):
		foreach($results as $r):?>
			<div class="line">
				<a href="<?php echo $r['href']; ?>">
					<img src="/img/200/100/<?php echo $r['meta']['thumbnail']; ?>" alt="<?php echo $r['meta']['title']; ?>">
				</a>
				<div>
					<h2><a href="<?php echo $r['href']; ?>"><?php echo $r['meta']['h1']; ?></a></h2>
					<?php echo $r['excerpt']; ?>
					<div style="margin-bottom: 10px;">
						<?php foreach($r['meta']['tags'] as $t): ?>
							<span class="tag"><a href="/tag/<?php echo slugify($t); ?>"><?php echo $t; ?></a></span>
						<?php endforeach; ?>
					</div>
					<a class="button" href="<?php echo $r['href']; ?>">Learn more >>></a>
				</div>
			</div>
		<?php endforeach;
	else : ?>
		<p style="color : Grey; font-size : 25px;">No results</p>
	<?php endif; ?>
</div>
