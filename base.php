<!DOCTYPE html>
<html lang="en" dir="ltr">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<!-- META DATA -->
		<?php if(!empty($data['meta']['description'])): ?>
			<meta name="description" content="<?php echo $data['meta']['description']; ?>">
		<?php endif; ?>
		<?php if(!empty($data['meta']['tags'])): ?>
			<meta name="keywords" content="<?php echo implode(', ', $data['meta']['tags']); ?>">
		<?php endif; ?>

		<!-- FAVICON -->
		<link href="/favicon.ico" rel="icon" type="image/x-icon"/>

		<!-- PAGE TITLE -->
		<?php if(!empty($data['meta']['title'])): ?>
			<title><?php echo $data['meta']['title']; ?></title>
		<?php elseif(!empty($params['website_name'])): ?>
			<title><?php echo $params['website_name']; ?></title>
		<?php endif;?>

		<!-- GLOBAL STYLESHEET -->
		<link rel="stylesheet" href="/style.css">

		<!-- Code snippets syntax highlighter : HIGHLIGHT JS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/monokai-sublime.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

		<!-- PAGE DEPENDANT IMPORTS -->
		<?php if(!empty($data['imports'])): ?>
			<?php include($data['imports']); ?>
		<?php endif; ?>
	</head>
	<body>
		<header>
			<!-- LOGO + WEBSITE NAME -->
			<div class="line">
				<?php if(!empty($config['website-name'])): ?>
					<a href="/"><img src="/img/150/50/logo.jpg" title="<?php echo $config['website-name']; ?>"></a>
					<p><?php echo $config['website-name']; ?></p>
				<?php endif; ?>
			</div>

			<!-- MAIN MENU -->
			<nav>
				<?php if(!empty($menu)): ?>
					<ul>
						<?php foreach($menu as $m => &$href):?>
							<li><a href="<?php echo $href; ?>"><?php echo $m; ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</nav>

			<!-- SEARCH INPUT -->
			<div id="search">
				<form action="/search" method="get">
					<input name="q" type="text" placeholder="Tag, keywords ..."/>
					<input type="submit" value="Search"/>
				</form>
			</div>
		</header>

		<!-- BREADCRUMB -->
		<?php if(!empty($thread)): ?>
			<nav aria-label="Breadcrumb" class="breadcrumb">
			    <ul>
					<?php foreach($thread as $name => $link):
						if($name != array_key_last($thread) && !empty($link)):?>
							<li><a href="<?php echo $link; ?>"><?php echo $name; ?></a> >></li>
						<?php else :?>
							<li><span aria-current="page"><?php echo $name; ?></span></li>
						<?php endif;
					endforeach;?>
			    </ul>
			</nav>
		<?php endif;?>

		<!-- CURRENT PAGE TEMPLATE -->
		<?php include($template); ?>

		<footer>
			<!-- FOOTER MENU -->
			<?php if(!empty($footer_menu)): ?>
				<ul>
					<?php foreach($footer_menu as $m => &$href):?>
						<li><a href="<?php echo $href; ?>"><?php echo $m; ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif;?>
		</footer>

		<!-- PAGE DEPENDANT STYLE -->
		<?php if(!empty($data['css'])):?>
			<style>
				<?php echo $data['css']; ?>
			</style>
		<?php endif; ?>
		<?php if(!empty($data['css_intag'])){
			echo $data['css_intag'];
		} ?>

		<!-- SCRIPTS USED THE ENTIRE WEBSITE -->
		<script>
			// -- CODE SNIPPETS COPY BUTTON --
			function fallbackCopyTextToClipboard(text) {
				var textArea = document.createElement("textarea");
				textArea.value = text;

				// Avoid scrolling to bottom
				textArea.style.top = "0";
				textArea.style.left = "0";
				textArea.style.position = "fixed";

				document.body.appendChild(textArea);
				textArea.focus();
				textArea.select();

				try {
					var successful = document.execCommand('copy');
					var msg = successful ? 'successful' : 'unsuccessful';
					console.log('Fallback: Copying text command was ' + msg);
				} catch (err) {
					console.error('Fallback: Oops, unable to copy', err);
				}

				document.body.removeChild(textArea);
			}

			function htmlDecode(input) {
				var doc = new DOMParser().parseFromString(input, "text/html");
				return doc.documentElement.textContent;
			}

			function copyTextToClipboard(text) {
				var t = htmlDecode(text);
				if (!navigator.clipboard) {
					fallbackCopyTextToClipboard(t);
					return;
				}
				navigator.clipboard.writeText(t);
			}

			function fade(element) {
				var op = 1;  // initial opacity
				var timer = setInterval(function () {
					if (op <= 0.1){
						clearInterval(timer);
						element.style.display = 'none';
					}
						element.style.opacity = op;
						element.style.filter = 'alpha(opacity=' + op * 100 + ")";
					op -= op * 0.1;
				}, 50);
			}
			const contents = [];

			document.querySelectorAll('pre').forEach((el, index) => {
				contents[index] = el.querySelector('code').innerHTML;

				el.innerHTML = '<button class="copy-button">Copy</button>' + el.innerHTML;
				el.addEventListener("mouseleave", function(e){
					el.querySelector('button').innerHTML = 'Copy';
				});

				el.addEventListener("click", function(e){
					var btn = el.querySelector('button');

					copyTextToClipboard(contents[index]);
					el.querySelector('button').innerHTML = 'Copied !';
				});
			});

			// -- HIGHLIGHT JS --
			hljs.highlightAll();
		</script>

		<!-- PAGE DEPENDANT JAVASCRIPT -->
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
