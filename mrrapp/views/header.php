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
		<link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">

		<? /* Hojas de estilo complementarias */ ?>
		<link rel="stylesheet" type="text/css" href="<?= base_url('js/bxslider/jquery.bxslider.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url('js/intl-tel-input/css/intlTelInput.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url('js/sweetalert/sweetalert.css'); ?>">
		<link rel="stylesheet" type="text/css" href="<?= base_url('js/fancybox/jquery.fancybox.css'); ?>" media="screen" />

		<? /* Favicon */ ?>
		<link rel="icon" type="image/png" href="<?= base_url('images/logo-myringring.png'); ?>">

		<? /* Etiquetas responsive */ ?>
		<meta name="viewport" content="width=device-width">
		<meta name="format-detection" content="telephone=no">

		<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
		<script src="<?= base_url('js/jquery.validate.min.js'); ?>"></script>
		<script src="<?= base_url('js/bxslider/jquery.bxslider.min.js'); ?>"></script>
		<script src="<?= base_url('js/intl-tel-input/js/intlTelInput.js'); ?>"></script>
		<script src="<?= base_url('js/intl-tel-input/js/utils.js'); ?>"></script>
		<script src="<?= base_url('js/sweetalert/sweetalert.min.js'); ?>"></script>
		<script src="<?= base_url('js/fancybox/jquery.fancybox.pack.js'); ?>"></script>
		<script src="<?= base_url('js/modules/utilities.js'); ?>"></script>

		<script type="text/javascript"><!--
			document.createElement("nav");
			document.createElement("header");
			document.createElement("footer");
			document.createElement("section");
			document.createElement("article");
			document.createElement("aside");
			document.createElement("hgroup");

			function changeLanguage(lang)
			{
				var url = '<?= base_url('headerCtrl/changeLanguage/'); ?>';
				$.ajax({
					url: url,
					type: 'POST',
					data: {lang: lang},
				})
				.done(function() {
					location.reload();
				})
				.fail(function(){
					console.log('error');
				})
				.always(function(){
					console.log('complete');
				});
			}

			function viewInvoice(){
				var url = '<?= base_url('report/viewInvoice/'); ?>';
				$.ajax({
					url: url,
					type: 'POST',
					data: {lang: lang},
				})
				.done(function() {
				})
				.fail(function(){
					console.log('error');
				})
				.always(function(){
					console.log('complete');
				});
			}
		--></script>
		<? /* Hoja de estilo principal */ ?>
		<link type="text/css" href="<?= base_url('css/style.css'); ?>" rel="stylesheet">
	</head>
	<body>
		<div class="pageHeader">
			<div class="centeredContent">
				<div class="logo">
					<a href="<?= base_url(); ?>"><img src="<?= base_url('images/logo-myringring.png'); ?>" alt=""></a>
				</div>
				<div class="headerRight">
					<div class="topMenu">
						<div class="fRight">
							[ <strong><?= $_SESSION['userName']; ?></strong> ]&nbsp; &nbsp;
							<a href="<?= base_url('home/account'); ?>"><?= lang('my_account'); ?></a>&nbsp; |&nbsp;
							<a href="<?= base_url('home/logout'); ?>"><?= lang('logout'); ?></a>
						</div>
						<div class="clear10"></div><?

						if($_SESSION['userType'] == STORE)
						{
							$balanceStore = getBalanceStore(); ?>
							<?= lang('balance'); ?>  <strong>$<?= number_format($balanceStore->balance, 2, '.', ','); ?></strong>&nbsp; |&nbsp;
							<?= lang('available'); ?>  <strong>$<?= number_format($balanceStore->available, 2, '.', ','); ?></strong>&nbsp;&nbsp;&nbsp;
							<a href="" onclick="changeLanguage('en')"><img src="<?= base_url('images/EN.png'); ?>" alt="">&nbsp;<?= lang('english'); ?></a>&nbsp; |&nbsp;
							<a href="" onclick="changeLanguage('es')"><img src="<?= base_url('images/ES.png'); ?>" alt="">&nbsp;<?= lang('spanish'); ?></a><?
						} ?>
					</div>
					<div class="clear"></div>
					<ul class="pageMenu">
						<li><a href="<?= base_url('home'); ?>"><?= lang('home'); ?></a></li><?
						if($_SESSION['userType'] == STORE)
						{ ?>
							<li><a href="<?= base_url('pinlessAdminCtrl'); ?>"><?= lang('link_pinless'); ?></a></li>
							<li>
								<a href="#"><?= lang('customer_support'); ?></a>
								<ul class="submenu">
									<li><a href="<?= base_url('clientCtrl/transactionsList'); ?>"><?= lang('recent_transactions'); ?></a></li>
									<li><a href="<?= base_url('clientCtrl/pinlessAccess'); ?>"><?= lang('pinless_access'); ?></a></li>
									<li><a href="<?= base_url('clientCtrl/ratesList'); ?>"><?= lang('rates_list'); ?></a></li>
									<li><a href="<?= base_url('clientCtrl/feeList'); ?>"><?= lang('fee_list'); ?></a></li>
								</ul>
							</li>
							<li>
								<a href="#"><?= lang('reports'); ?></a>
								<ul class="submenu">
									<li><a href="<?= base_url('reportsCtrl/invoices'); ?>"><?= lang('reports_invoices'); ?></a></li>
									<li><a href="<?= base_url('report/recentDeposit'); ?>"><?= lang('my_payments'); ?></a></li>
									<li><a href="<?= base_url('report/salesByProduct'); ?>"><?= lang('sales_by_product'); ?></a></li>
									<li><a href="<?= base_url('report/guestsNumbers'); ?>"><?= lang('guest_numbers'); ?></a></li>
									<!--li><a href="<?= base_url('report/pdf'); ?>">Pdf</a></li-->
								</ul>
							</li><?
						}
						elseif($_SESSION['userType'] == ADMIN)
						{ ?>
							<li><a href="<?= base_url('depositCtrl'); ?>">Deposit</a></li>
							<li><a href="<?= base_url('admin/transactionsList'); ?>">Recent transactions</a></li>
							<li>
								<a href="#">Reports</a>
								<ul class="submenu">
									<li><a href="<?= base_url('reportsCtrl/invoices'); ?>"><?= lang('reports_invoices'); ?></a></li>
									<li><a href="<?= base_url('report/recentDeposit'); ?>">Recent deposits</a></li>
									<li><a href="<?= base_url('report/invoices'); ?>">Invoices</a></li>
									<li><a href="<?= base_url('report/reconcile'); ?>">Reconcile</a></li>
									<li><a href="<?= base_url('report/salesBySeller'); ?>">Sales by agent</a></li>
									<li><a href="<?= base_url('report/sellerEarnings'); ?>">Agents earnings</a></li>
									<li><a href="<?= base_url('report/companyEarnings'); ?>">Company earnings</a></li>
								</ul>
							</li>
							<li>
								<a href="#">Admin</a>
								<ul class="submenu">
									<li><a href="<?= base_url('admin/usersList'); ?>">Users</a></li>
									<li><a href="<?= base_url('report/guestsNumbers'); ?>">Guest's Numbers</a></li>
									<li><a href="<?= base_url('admin/sellersList'); ?>">Agents</a></li>
									<li><a href="<?= base_url('admin/storesList'); ?>">Stores</a></li>
									<li><a href="<?= base_url('admin/providersList'); ?>">Providers</a></li>
									<li><a href="<?= base_url('admin/countriesList'); ?>">Countries</a></li>
									<li><a href="<?= base_url('admin/productsList'); ?>">Products</a></li>
									<li><a href="<?= base_url('content/newsList'); ?>">News</a></li>
									<li><a href="<?= base_url('content/slidesList'); ?>">Slides</a></li>
									<li><a href="<?= base_url('admin/contents'); ?>"><?= lang('contents'); ?></a></li>
								</ul>
							</li><?
						}
						elseif($_SESSION['userType'] == SELLER)
						{ ?>
							<li><a href="<?= base_url('seller/storesList'); ?>">Stores</a></li>
							<li><a href="<?= base_url('depositCtrl'); ?>">Deposit</a></li>
							<li><a href="<?= base_url('seller/transactionsList'); ?>">Recent transactions</a></li>
							<li>
								<a href="#">Reports</a>
								<ul class="submenu">
									<li><a href="<?= base_url('reportsCtrl/invoices'); ?>"><?= lang('reports_invoices'); ?></a></li>
								</ul>
							</li>
						  <?
						} ?>
					</ul>
				</div>
				<div class="topPhone">
					<p><?= lang('customer_service'); ?> <a href="tel:18888137485">1 888 8137485</a></p>
				</div>
				<div class="clear"></div>
			</div>
		</div>