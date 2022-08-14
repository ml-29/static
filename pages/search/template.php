<?php
	$results = [];
	$search = "";
	if(isset($_GET['q'])){
		$search = strtolower(trim($_GET['q']));
		// echo $search;
		$search_terms = explode(' ', $search);
		$posts = Data::listPosts();

		foreach($posts as $p){
			$h1 = strtolower($p['meta']['h1']);
			// echo $p['meta']['h1'];
			if(str_contains($h1, $search)){//Full-sentence matches in post title
				array_push($results, $p);
			}else{
				$all_search_terms = true;
				foreach($search_terms as $t){//check that All search terms in post title
				   if(!str_contains($h1, $search)){
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
					   if(str_contains($h1, $search)){
						   $any_search_term = true;
					   }
				   }
				   if($any_search_term){
					   //push
					   array_push($results, $p);
				   }elseif(str_contains(strtolower($p['content']), $search)){//Full-sentence matches in post content.
					   //push
					   array_push($results, $p);
				   }elseif(array_key_exists('tags', $p['meta']) && in_array($search, $p['meta']['tags'])){//search full search string in tags
					   //push
					   array_push($results, $p);
				   }
				}
			}
		}
	}
	// var_dump($results);
?>
<div class="accent">
	<?php if($search): ?>
		<h1>Results for "<?php echo $search; ?>"</h1>
	<?php else:?>
		<h1>Search</h1>
	<?php endif; ?>
</div>
<div class="container">
	<?php if(!empty($results)):
		foreach($results as $r):?>
			<div class="line">
				<?php if(array_key_exists('thumbnail', $r['meta'])): ?>
					<a href="<?php echo $r['href']; ?>">
						<img src="/img/200/100/<?php echo $r['meta']['thumbnail']; ?>" alt="<?php echo $r['meta']['title']; ?>">
					</a>
				<?php endif; ?>
				<div>
					<h2><a href="<?php echo $r['href']; ?>"><?php echo $r['meta']['h1']; ?></a></h2>
					<?php echo $r['excerpt']; ?>
					<?php if(array_key_exists('tags', $r['meta'])): ?>
						<div style="margin-bottom: 10px;">
							<?php foreach($r['meta']['tags'] as $t): ?>
								<span class="tag"><a href="/tag/<?php echo slugify($t); ?>"><?php echo $t; ?></a></span>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<a class="button" href="<?php echo $r['href']; ?>">Learn more >>></a>
				</div>
			</div>
		<?php endforeach;
	else : ?>
		<p style="color : Grey; font-size : 25px;">No results</p>
	<?php endif; ?>
</div>
