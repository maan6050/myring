<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?= $title; ?> | My Ring Ring</title>

		<? /* Fuentes */ ?>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
		<link href="https://fonts.googleapis.com/css?family=Oxygen:400,700" rel="stylesheet">

		<? /* Favicon */ ?>
		<link rel="icon" type="image/png" href="<?= base_url('/images/logo-myringring.png'); ?>">

		<? /* Etiquetas responsive */ ?>
		<meta name="viewport" content="width=device-width">
		<meta name="format-detection" content="telephone=no">

		<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="<?= base_url('/js/jquery.validate.min.js'); ?>"></script>

		<script type="text/javascript"><!--
			document.createElement("nav");
			document.createElement("header");
			document.createElement("footer");
			document.createElement("section");
			document.createElement("article");
			document.createElement("aside");
			document.createElement("hgroup");
		--></script>
		<? /* Hoja de estilo principal */ ?>
		<link type="text/css" href="<?= base_url('css/style.css'); ?>" rel="stylesheet">
	</head>
	<body class="page-template-page-login-php"><?
		if(isset($error))
		{ ?>
			<script type="text/javascript"> alert('<?= $error; ?>'); </script><?
		} ?>
		<div class="loginPane">
			<div class="alignCenter"><img src="<?= base_url('/images/logo-myringring.png'); ?>" alt=""></div>
			<form name="login" id="login" method="post" action="<?= base_url('login/authenticate'); ?>">
				<label for="un">Username: </label>
				<input type="text" name="un" id="un" value="<? if(isset($un)) echo $un; ?>" placeholder="Email" required>
				<label for="pw">Password: </label>
				<input type="password" name="pw" id="pw" placeholder="Password" required>
				<div class="alignCenter">
					<input type="submit" value="Log In">
				</div>
				<div class="clear10"></div>
				<div class="clear10"></div>
				<div class="clear10"></div>
				<div class="footer">
					<p>Customer service: <strong><a href="tel:+18888137485">1 888 8137485</a></strong></p>
					<p><small>All rights reserved &copy;</small></p>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#login').validate({
					errorPlacement: function(error, element){
						// Adiciona el error dentro de la etiqueta asociada.
						$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
					},
					errorElement: 'em'
				});
			});
		</script>
	</body>
</html>