<div class="accent">
	<h1>Contact</h1>
</div>
<div class="container">
	<?php echo $data['content']; ?>

	<?php
	$errors = [];
	$successes = [];
	if(
		isset($_POST['first-name']) &&
		isset($_POST['last-name']) &&
		isset($_POST['e-mail']) &&
		isset($_POST['phone']) &&
		isset($_POST['message'])
	){
		$first_name = htmlspecialchars($_POST['first-name']);
		$last_name = htmlspecialchars($_POST['last-name']);
		$e_mail = htmlspecialchars(filter_var($_POST['e-mail'], FILTER_SANITIZE_EMAIL));
		$phone = htmlspecialchars($_POST['phone']);
		$message = htmlspecialchars($_POST['message']);

		//send the e-mail
		$to = $config['contact-e-mail-address'];
		$subject = $config['website-name'] . " Contact us form - " . $first_name . " " . $last_name ;
		$message = "From : " . $first_name . " " . $last_name . "\r\n" . "Tel: " . $phone . "\r\n" . "E-mail : " . $e_mail . "\r\n" . "Message : " . "\r\n" . $message;
		$headers = 'From: ' . $e_mail . "\r\n" . 'Reply-To: ' . $e_mail . "\r\n" . 'X-Mailer: PHP/' . phpversion();

		if(mail($to, $subject, $message, $headers)){
			array_push($successes, "Your message was sent successfully");
		}else{
			array_push($errors, "Failed to send message");
		}
	}?>

	<?php foreach($errors as $e):?>
		<div class="error"><?php echo $e; ?></div>
	<?php endforeach; ?>

	<?php foreach($successes as $s):?>
		<div class="success"><?php echo $s; ?></div>
	<?php endforeach; ?>

	<form method="post" action="#">
		<input type="text" name="first-name" placeholder="First name">
		<input type="text" name="last-name" placeholder="Last name">
		<input type="email" name="e-mail" placeholder="Email address">
		<input type="tel" name="phone" placeholder="Phone number">
		<textarea name="message" cols="40" rows="10"></textarea>
		<input type="submit" name="submit" value="Send">
	</form>

</div>
