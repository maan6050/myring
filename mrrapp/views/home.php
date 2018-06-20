<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(isset($error) || isset($status))
{ ?>
	<div id="msg" class="phonePane"><?
		if(isset($error) && !isset($status))
		{ ?>
			<p class="error"><?= $error; ?></p><?
		}
		if(isset($status))
		{
			if($status == 'Success')
			{ ?>
				<p>
					<strong class="blue">$<?= $amount; ?></strong> <?= lang('status_success'); ?><strong> <?= $phone; ?></strong>.
					<a href="<?= base_url('printThermo/index/'.$id); ?>" data-fancybox-type="iframe" class="fancybox"><?= lang('print_receipt'); ?></a>
				</p>
				<div class="clear10"></div>
				<p><strong><?= lang('start_over'); ?> <?= $clientPhone; ?>? <a id="startOver" data-clientPhone="<?= $clientPhone; ?>"><?= lang('click_here'); ?></a></strong></p><?
			}
			elseif($status == 'Pending')
			{ ?>
				<p class="blue"><img src="<?= base_url('images/loading.gif'); ?>" class="loading"><?= lang('status_pending'); ?></p><?
			}
			else
			{ ?>
				<p class="error"><?= $error; ?></p>
				<div class="clear10"></div>
				<p><strong><?= lang('start_over'); ?> <?= $clientPhone; ?>? <a id="startOver" data-clientPhone="<?= $clientPhone; ?>"><?= lang('click_here'); ?></a></strong></p><?
			}
		} ?>
	</div><?
} ?>
<div class="phonePane">
	<form name="addFunds" id="addFunds" method="post" action="<?= base_url('home/addFunds'); ?>">
		<input type="hidden" name="productId" id="productId" value="">
		<div id="divClientPhone">
			<h1><label for="clientPhone"><?= lang('title_customers_phone'); ?></label></h1>
			<h1>+1</h1><input type="number" name="clientPhone" id="clientPhone" placeholder="<?= lang('placeholder_phone'); ?>" value="" minlength="10" maxlength="10" required>
			<input type="button" id="search" name="search" value="<?= lang('bttn_search'); ?>" class="searchButton">
			<input type="button" id="guest-btn" name="guest" value="<?= lang('bttn_guest'); ?>" class="searchButton">
			<div class="clear10"></div>
			<div class="clear10"></div>
		</div>
		<div id="recipientDiv" style="display:none;">
			<div class="clear10"></div>
			<div id="frequentPurchasesDiv">
				<fieldset>
					<legend><?= lang('frequent_purchases'); ?></legend>
					<table class="recipientList" cellpadding="0" cellspacing="0">
						<thead>
							<tr><th nowrap><?= lang('col_product'); ?></th><th nowrap><?= lang('col_name'); ?></th><th nowrap><?= lang('col_recipient_phone'); ?></th><th nowrap><?= lang('col_amount'); ?></th><th></th></tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</fieldset>
				<strong style="color:#333; font-weight:bold; display:block; margin:10px 0"><?= lang('not_on_the_list'); ?></strong>
			</div>
			<div class="clear10"></div>
			<h1><label for="phoneInput"><?= lang('recipient_phone'); ?></label></h1>
			<div class="disblePhoneInput"></div>
			<input type="tel" name="phoneInput" id="phoneInput" placeholder="<?= lang('placeholder_phone'); ?>" value="" maxlength="15" required>
			<input type="hidden" name="phone" id="phone">
			<div class="clear10"></div>
			<div class="clear10"></div>
			<input type="button" id="continue" name="continue" value="<?= lang('bttn_continue'); ?>">
			<div class="clear"></div>
			<br />
			<div id="productDiv" style="display:none;">
				<div id="product">
					<a class="anotherCarrier"><?= lang('another_carrier'); ?></a>
					<div class="clear"></div>
					<div class="tabs-container">
						<ul class="tabs products">
							<li class="tab-link current" data-tab="tab-1">
								<h3><?= lang('select_topup_rtr'); ?></h3>
							</li>
							<li class="tab-link" data-tab="tab-2">
								<h3><?= lang('select_topup_pin'); ?></h3>
							</li>
						</ul>
						<div id="tab-1" class="tab-content products current">
							<div class="rtr-products">
							</div>
							<div class="clear"></div>
						</div>
						<div id="tab-2" class="tab-content products">
							<div class="pin-products">
							</div>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<br />
			<div id="amountDiv" style="display:none;">
				<h1><label for="amount"><?= lang('amount'); ?></label></h1>
				<div id="typeDiv">
					<input type="number" name="amount" id="amount" placeholder="Number" value="" required>
				</div>
				<div class="clear10"></div>
				<div class="clear10"></div>
				<label>&nbsp;</label>
				<div id="divServiceCharge">&nbsp;&nbsp;&nbsp;<?= lang('additional_charge') ?> $<strong id="serviceCharge">0</strong></div>
				<div class="clear10"></div>
				<input type="submit" value="<?= lang('bttn_accept') ?>">
			</div>
		</div>
	</form>
	<form name="repurchase" id="repurchase" method="post" action="<?= base_url('home/addFunds'); ?>">
		<input type="hidden" name="amount" id="recAmount" value="">
		<input type="hidden" name="productId" id="recProductId" value="">
		<input type="hidden" name="phone" id="recPhone" value="">
		<input type="hidden" name="clientPhone" id="recClientPhone" value="">
		<input type="hidden" name="name" id="recName" value="">
	</form>
	<div class="clear"></div>
</div>
<div class="centeredContent">
	<div class="sliderContent">
		<ul class="bxslider"><?
			foreach($slides as $s)
			{ ?>
				<li class="sliderItem" style="background-image:url(<?= base_url(UPLOADS.$s->image); ?>)"></li><?
			} ?>
		</ul>
	</div>
	<div class="newsContent">
		<h2 class="newsTitle"><?= lang('latest_news'); ?></h2>
		<div class="newsDiv"><?
			foreach($news as $n)
			{ ?>
				<a href="#news<?= $n->id; ?>" class="nDiv readMore">
					<div class="fLeft">
						<h4><?= $n->title; ?></h4>
						<span class="nDate"><?= $n->created; ?></span>
					</div>
					<div class="fRight" style="background-image:url(<?= base_url(UPLOADS.$n->image); ?>)"></div>
					<div class="clear"></div>
				</a><?
			} ?>
		</div>
	</div>
</div>
<div class="hide"><?
	foreach($news as $n)
	{ ?>
		<div id="news<?= $n->id; ?>" class="nDiv">
			<div class="fLeft">
				<h4><?= $n->title; ?></h4>
				<em><?= $n->created; ?></em>
				<div class="clear10"></div>
				<div class="pLeft20" style="float:right;">
					<img src="<?= base_url(UPLOADS.$n->image); ?>" width="150">
				</div>
				<p align="justify"><?= nl2br($n->content); ?></p>
			</div>
		</div><?
	} ?>
</div>
<div class="homeButtons">
	<a id="abroad"><?= lang('bttn_intl_topup'); ?></a>
	<a id="domestic"><?= lang('bttn_domestic_topup'); ?></a>
	<a id="pinless"><?= lang('bttn_pinless_topup'); ?></a>
</div>
<div class="orangeMenu">
	<i class="fa fa-shopping-cart ico-shopping-car" aria-hidden="true"></i>
	<div class="orangeMenuSeparator"></div>
	<p class="orangeMenuSmallText">
		<span id="productsAmount"><?= $cart['qty']; ?></span> <?= $cart['qty'] == 1 ? lang('cart_topup_made') : lang('cart_topups_made'); ?>
	</p>
	<p class="orangeMenuLargeText">
		Total<br>$<span id="productsCost"><?= $cart['amount']; ?></span>
	</p>
	<i id="orangeMenuArrow" class="fa fa-angle-double-left" aria-hidden="true"></i>
</div>
<div class="shoppingCartContent" class="hide">
	<div class="shoppingCartTitle"><h2><?= $cart['clientPhone']; ?></h2></div>
	<ul class="shoppingCartHeader">
		<li class="shoppingCartCant"><?= lang('cart_phone'); ?></li>
		<li class="shoppingCartProd"><?= lang('cart_status'); ?></li>
		<li class="shoppingCartPrec"><?= lang('cart_cost'); ?></li>
	</ul>
	<div class="shoppingCartList"><?
		if(count($cart['tr']) > 0)
		{ ?>
			<ul class="shoppingCartCategories"><?
				foreach($cart['tr'] as $tr)
				{ ?>
					<li class="shoppingCartCategory">
						<span><?= $tr[0]; ?></span>
						<span class="fRight">$<strong><?= $tr[1]; ?></strong></span>
					</li><?
				} ?>
			</ul><?
		}
		else
		{ ?>
			<p class="shoppingEmptyCart"><?= lang('cart_no_topup'); ?></p><?
		} ?>
	</div>
	<div class="shoppingCartFooter">
		<div class="span7">Total</div>
		<div class="span5 shoppingCartTotal">$<?= $cart['amount']; ?></div>
	</div>
</div>
<script type="text/javascript">
	// Determina el paso en el que va el proceso de recarga.
	var addFundsStatus = 0, repurchaseStatus = 0;

	jQuery(document).ready(function($){
		// Pongo el cursor en el número del cliente.
		$('#clientPhone').focus();

		UtilModule.initTabs();

		$('#addFunds').submit(function(e){
			switch(addFundsStatus)
			{
				case 0:
					e.preventDefault(e);
					// Valido que haya digitado el teléfono.
					if(validator.element('#clientPhone')){
						showFrequentPurchases();
					}
					break;
				case 1:
					e.preventDefault(e);
					// Valido que haya digitado el teléfono del receptor.
					if(validator.element('#phoneInput')){
						$('#phone').val($('#phoneInput').intlTelInput('getNumber'));
						showProducts();
					}
					break;
				case 2:
					e.preventDefault(e);

					var amount = 0;

					if ($('ul.skus-container').length > 0) {
						amountSelected = $('[name="amountfromlist"]:checked');
						if (amountSelected.hasClass('open-amount')) {
							amount = amountSelected.parent().find('input.amount-input-text').val();
						} else {
							amount = amountSelected.val();
						}

					} else {
						amount = $('#amount :selected').html() != undefined ? $('#amount :selected').html() : $('#amount').val();
					}
					// Si no hay definido un valor no se permite seguir la ejecucion.
					if (isNaN(amount)) { return; }

					var total = parseFloat(amount) + parseFloat($('#serviceCharge').html());
					swal({
						title: '<?= lang('msg_confirm_topup'); ?>' + $('#phone').val() + '<?= lang('msg_confirm_topup2'); ?>' + total + '?',
						text: '',
						type: 'warning',
						showCancelButton: true,
						cancelButtonText: '<?= lang('bttn_cancel'); ?>',
						confirmButtonColor: '#D29105',
						confirmButtonText: '<?= lang('bttn_continue'); ?>',
						closeOnConfirm: true
					},
					function(isConfirm){
						if (isConfirm)
						{
							// Aceptó realizar la transacción.
							addFundsStatus = 3;
							$('#addFunds').submit();
						}
					});
					break;
				case 3:
					// Aquí se envía el formulario y cambio la bandera para que no vuelva a entrar.
					addFundsStatus = 4;
					break;
				default:
					// En cualquier otro caso no envía el formulario.
					e.preventDefault(e);
			}
		});

		$('#search').click(function(){
			// Valido que haya digitado el teléfono.
			if(validator.element('#clientPhone'))
			{
				showFrequentPurchases();
				$('.homeButtons').slideUp();
			}
		});

		$('input#guest-btn').click(function(evt) {
			evt.preventDefault(evt);
			var elm = $('input#clientPhone');
			if (elm.length > 0) {
				elm.val('1111111111');
			}
			return false;
		});

		function showFrequentPurchases(){
			if($('#recipientDiv').is(':visible')){
				$('#recipientDiv').slideUp();
			}
			$.ajax({
				url: '<?= base_url('home/getRecipients/'); ?>' + $('#clientPhone').val(),
				type: 'GET',
				dataType: 'json',
				success: function(items){
					// Avanzo al siguiente paso.
					addFundsStatus = 1;
					// Borro el contenido actual.
					$('.recipientList tbody').empty();
					if(items.length > 0){
						$('#frequentPurchasesDiv').show();
						$.each(items, function(i, item){
							if(item.type == 'f'){
								// Valores fijos.
								var types = item.values.split(',');
								var amount = '<select name="amount" id="amount' + i + '" style="border:1px solid #D5D8DE; font-size:24px; height:40px;min-width:100px; border-radius:3px">';
								var value = tag = element = '';
								$.each(types, function(i, item){
									// Determino si tiene una etiqueta diferente al valor a recargar.
									if(item.indexOf('|') != -1){
										// Separo el valor de la etiqueta.
										element = item.split('|', 2);
										value = element[0];
										tag = element[1];
									}else{
										value = tag = item;
									}
									amount += '<option value="' + value + '">' + tag + '</option>';
								});
								amount += '</select>';
							}else{
								// Valores abiertos en un rango.
								var range = item.values.split(',');
								var placeholder = 'Between ' + range[0] + ' and ' + range[1];
								var amount = '<label for="amount' + i + '"></label><input type="number" name="amount" id="amount' + i + '" placeholder="' + placeholder + '" value="" required>';
							}
							var name = !item.name ? '' : item.name;
							var row = '<tr id="id' + i + '">' +
								'<td class="logoRList">' + item.image + item.productName + '</td>' +
								'<td><input type="text" name="name" id="name' + i + '" value="' + name + '"></td>' +
								'<td>' + item.phone + ' <small><a data-i="' + i + '" data-phone="' + item.phone + '" data-productId="' + item.productId + '" class="deletePhone">Delete this number</a></small></td>' +
								'<td>' + amount + '</td>' +
								'<td><input type="button" id="accept' + i
								+ '" value="Accept" data-i="' + i
								+ '" data-productId="' + item.productId
								+ '" data-phone="' + item.phone
								+ '" data-servicecharge="' + item.serviceCharge
								+ '" data-includecharge="' + item.includeCharge
								+ '" class="btnPhonebook"></td>'
								+ '</tr>';
							$('.recipientList').append(row);
						});
					}else{
						$('#frequentPurchasesDiv').hide();
					}
					$('#recipientDiv').slideDown('slow');
				},
				error: function(xhr, status) {
					alert('Something failed. Please reload the page and try again.');
				}
			});
		}

		$('#frequentPurchasesDiv').on('click', '.deletePhone', {}, function(e){
			var id = $(this).attr('data-i');
			$.ajax({
				url: '<?= base_url('home/deletePhonebook/'); ?>' + $('#clientPhone').val() + '/' + $(this).attr('data-phone') + '/' + $(this).attr('data-productId'),
				type: 'GET',
				dataType: 'json',
				success: function(item){
					if(item.status == 'ok'){
						// Borró bien el número, elimina la fila.
						$('#id' + id).fadeOut();
					}
				},
				error: function(xhr, status) {
					alert('Something failed. Please reload the page and try again.');
				}
			});
		});

		$('#frequentPurchasesDiv').on('click', '.btnPhonebook', {}, function(){
			var i = $(this).attr('data-i');
			if(validator.element('#amount' + i)){
				//var amount = $('#amount' + i + ' :selected').html() != undefined ? $('#amount' + i + ' :selected').html() : $('#amount' + i).val();
				var amount = parseFloat($('#amount' + i).val());
				var amountToShow = 0;
				serviceCharge = parseFloat($(this).attr('data-servicecharge'));
				includeCharge = parseFloat($(this).attr('data-includecharge'));

				if(serviceCharge > 0 || includeCharge > 0){
					amountToShow = amount + serviceCharge;  // (amount - includeCharge) + (serviceCharge + includeCharge)
				}else{
					amountToShow = $('#amount' + i + ' :selected').html() != undefined ? $('#amount' + i + ' :selected').html() : $('#amount' + i).val();
				}
				if(repurchaseStatus == 0){
					swal({
						title: '<?= lang('msg_confirm_topup'); ?>' + $(this).attr('data-phone') + '<?= lang('msg_confirm_topup2'); ?>' + amountToShow + '?',
						text: '',
						type: 'warning',
						showCancelButton: true,
						cancelButtonText: $('#bttn_cancel').val(),
						confirmButtonColor: '#D29105',
						confirmButtonText: $('#bttn_continue').val(),
						closeOnConfirm: true
					},
					function(){
						// Evito que el formulario se envíe varias veces.
						repurchaseStatus = 1;
						$('#recClientPhone').val($('#clientPhone').val());
						$('#recProductId').val($('#accept' + i).attr('data-productId'));
						$('#recName').val($('#name' + i).val());
						$('#recPhone').val($('#accept' + i).attr('data-phone'));
						$('#recAmount').val(amount);
						$('#repurchase').submit();
					});
				}
			}
		});

		$("#phoneInput").blur(function(){
			var phonePlusIndicative = $("#phoneInput").intlTelInput("getNumber");
			// Agrega un 1 al indicativo de México +52.
			phonePlusIndicative = phonePlusIndicative.replace('+52', '+521');
			$("#phone").val(phonePlusIndicative);
		});

		// Si selecciona USA, copio el número del pagador.
		$('#phoneInput').on('countrychange', function(e, countryData){
			if(countryData.iso2 == 'us' && $('#phoneInput').val() == '' && $('#divClientPhone').is(':visible')){
				$('#phoneInput').val($('#clientPhone').val());
			}
		});

		function onlyNumbers(_elem){
			_elem.keypress(function(evt){
				var keyCode = evt.which ? evt.which : evt.keyCode;
				if(keyCode == 118){ return true; }
				if(keyCode != 8 && keyCode != 0 && (keyCode < 48 || keyCode > 57)){ return false; }
			});
		};

		function showValuesAsList(product) {
			var allowOpenAmount = product.attr('data-allowopenamount'),
				data, label, li, tag,
				ul = $('<ul/>', {
					class: 'clear skus-container'
				}),
				values = product.attr('data-values').split(',');

			if(allowOpenAmount == 1)
			{
				li = $('<li/>');

				label = $('<label/>').appendTo(li);

				var inputRadio = $('<input/>', {
					class: 'amount-input open-amount',
					name: 'amountfromlist',
					type: 'radio'
				});
				inputRadio.appendTo(label);

				$('<span/>', {
					text: 'Another amount $ '
				}).appendTo(label);

				var inputText = $('<input/>', {
					class: 'amount-input-text',
					maxlength: 3,
					type: 'text'
				});
				inputText.appendTo(label);

				inputText.blur(function() {
					inputRadio.val(inputText.val());
				}).on('input', function() {
					var _value = parseInt(this.value, 10);
					if (0 == _value) { this.value = 1; }
					if (_value > 100) { this.value = 100; }
				});

				inputText.focus(function() {
					inputRadio.attr('checked', true);
				});

				onlyNumbers(inputText);

				li.appendTo(ul);
			}

			$.each(values, function(i, item){
				if(item.indexOf('|') != -1){
					data = item.split('|', 2);
					value = data[0];
					tag = data[1];
				}else{
					value = tag = item;
				}
				li = $('<li/>');
				label = $('<label/>').appendTo(li);

				$('<input/>', {
					class: 'amount-input',
					name: 'amountfromlist',
					type: 'radio',
					value: value
				}).appendTo(label);

				$('<span/>', {
					text: ('$' + value)
				}).appendTo(label);
				li.appendTo(ul);
			});
			ul.appendTo($('div#typeDiv'));
		}

		$('#product').on('click', '.productBox', {}, function(e){
			$('#typeDiv').empty();  // Borro el contenido actual.
			$('.productBox').removeClass('selectedProductBox');
			$(this).addClass('selectedProductBox');
			// Asigno el identificador del producto.
			$('#productId').val($(this).attr('data-id'));
			// Determino si el producto tiene un cargo adicional.
			$('#serviceCharge').html($(this).attr('data-charge'));
			if($(this).attr('data-charge') == '0.00'){
				$('#divServiceCharge').fadeOut();
			}else{
				$('#divServiceCharge').fadeIn();
			}
			if($(this).attr('data-type') == 'f'){
				if($(this).attr('data-showaslist') == 1){
					showValuesAsList($(this));
				}else{
					// Valores fijos.
					var types = $(this).attr('data-values').split(',');
					var select = '<select name="amount" id="amount" style="border:1px solid #D5D8DE; font-size:30px; height:50px; width:430px;">';
					var value = tag = element = '';
					$.each(types, function(i, item){
						// Determino si tiene una etiqueta diferente al valor a recargar.
						if(item.indexOf('|') != -1){
							// Separo el valor de la etiqueta.
							element = item.split('|', 2);
							value = element[0];
							tag = element[1];
						}else{
							value = tag = item;
						}
						select += '<option value="' + value + '">' + tag + '</option>';
					});
					select += '</select>';
					$('#typeDiv').append(select);
				}
			}else{
				// Valores abiertos en un rango.
				var range = $(this).attr('data-values').split(',');
				var placeholder = 'Between ' + range[0] + ' and ' + range[1];
				$('#typeDiv').append('<input type="number" name="amount" id="amount" placeholder="' + placeholder + '" value="" required>');
				// Adiciono las validaciones del rango.
				$('#amount').rules('remove', 'min max');
				/*$('#amount').rules('add', {
					min: range[0],
					max: range[1]  // Valido que no sobrepase el máximo.
				}); */
			}
			$('#amountDiv').fadeIn(400, function(){
				$('html, body').animate({
					scrollTop: $('#amountDiv').offset().top
				}, 1000);
			});
		});

		$("#phoneInput").intlTelInput({
			onlyCountries: [<?= $ids; ?>],
			preferredCountries: [<?= $preferred; ?>],
			separateDialCode: true
		});

		$('#continue').click(function(){
			// Valido que haya digitado el teléfono del receptor.
			if(validator.element('#phoneInput')){
				var countryData = $("#phoneInput").intlTelInput('getSelectedCountryData');
				getProducts(countryData.iso2, function() {
					showProducts();
				});
			}
		});

		function addItemProduct(container, item){
			container.append('<div data-id="' + item.id
				+ '" data-ispin="' + item.isPIN
				+ '" data-isunlimited="' + item.isUnlimited
				+ '" data-allowopenamount="' + item.allowOpenAmount
				+ '" data-showaslist="' + item.showAsList
				+ '" data-mnc="' + item.mnc
				+ '" data-type="' + item.type
				+ '" data-values="' + item.values
				+ '" data-charge="' + item.charge
				+ '" class="hide productBox">'
				+ '<a class="productBtn">'
				+ item.image
				+ '<div class="clear"></div>'
				+ item.name
				+ '</a>'
				+ '</div>');
		}

		function getProducts(iso2, callback){
			$.ajax({
				url: '<?= base_url('home/getProducts/'); ?>' + iso2,
				type: 'GET',
				dataType: 'json',
				success: function(items) {
					var productContainer = $('div#product');
					var pinProductContainer = productContainer.find('div.pin-products');
					var rtrProductContainer = productContainer.find('div.rtr-products');

					pinProductContainer.empty();  // Borro el contenido actual.
					rtrProductContainer.empty();  // Borro el contenido actual.

					if(items.length > 0) {
						var hasPinProducts = false;
						var hasRtrProducts = false;

						$.each(items, function(i, item) {
							if(item.isPIN == 1){
								hasPinProducts = true;
								addItemProduct(pinProductContainer, item);
							}else{
								hasRtrProducts = true;
								addItemProduct(rtrProductContainer, item);
							}
						});

						if(!hasPinProducts){
							pinProductContainer.append('No products available.');
						}

						if(!hasRtrProducts){
							rtrProductContainer.append('No products available.');
						}
					}else{
						pinProductContainer.append('No products available.');
						rtrProductContainer.append('No products available.');
					}
					if(callback){
						callback();
					}
				},
				error: function(xhr, status){
					alert('Something failed. Please reload the page and try again.');
				}
			});
			if($('#amountDiv').is(':visible')){
				$('#amountDiv').fadeOut();
			}
		}

		function showProducts(){
			// Escondo los números frecuentes.
			$('#frequentPurchasesDiv').slideUp();
			$.ajax({
				url: '<?= base_url('home/getProductByPhone/'); ?>' + $('#phone').val(),
				type: 'GET',
				dataType: 'json',
				success: function(items){
					// Avanzo al siguiente paso.
					addFundsStatus = 2;
					var found = false;
					if(items.name != undefined){
						console.log('Product found: ' + items.name + ', MNC: ' + items.mnc);
						// Escondo el primer botón.
						//$('#continue').fadeOut(400, function(){
							// Recorro todos los productos a ver si encuentro el que busco.
							$('#product .productBox').each(function(index){
								if($(this).attr('data-mnc') != items.mnc && $(this).attr('data-isunlimited') == 0){
									// No es el que busco, lo escondo.
									//$(this).hide();
								}else{
									found = true;
									$(this).removeClass('hide');
									$('.anotherCarrier').show();
								}
							});
							// Sí found es false es porque el producto no se encontró en la bd. Vuelvo a mostrar todos los productos.
							if(!found){
								$('#product .productBox').each(function(index){
									//$(this).show();
									$(this).removeClass('hide');
									$('.anotherCarrier').hide();
								});
							}
							$('#productDiv').fadeIn();
							// Deshabilito la modificación del teléfono.
							//$('#phoneInput').attr('readonly', 'readonly');
							//$('.disblePhoneInput').show();
						//});
					}else{
						// Muestro el listado de todos los productos.
						console.log(items.error);
						$('#product .productBox').each(function(index){
							$(this).removeClass('hide');
						});
						$('.anotherCarrier').hide();
						$('#productDiv').fadeIn();
						/*
						$('#continue').fadeOut(400, function(){
							$('#productDiv').fadeIn();
							// Deshabilito la modificación del teléfono.
							$('#phoneInput').attr('readonly', 'readonly');
							$('.disblePhoneInput').show();
						}); */
					}
				},
				error: function(xhr, status) {
					alert('Something failed. Please reload the page and try again.');
				}
			});
		}

		// Esconde el enlace para ver los demás operadores.
		$('.anotherCarrier').click(function(){
			$('.anotherCarrier').fadeOut(400, function(){
				$('#product .productBox').each(function(index){
					$(this).fadeIn();
				});
			});
		});

		var validator = $('#addFunds').validate({
			rules: {
				phoneInput: {
					digits: true
				},
				amount: {
					min: 0,
					max: <?= $max; ?>
				}
			},
			errorPlacement: function(error, element){
				// Adiciona el error dentro de la etiqueta asociada.
				$(element).closest('form').find('label[for="' + element.attr('id') + '"]').append(error);
			},
			errorElement: 'em'
		});

		$('.bxslider').bxSlider({
			'controls':true,
			'auto':true,
			'pause':4000,
			'responsive':true,
			'prevText':'<i class="fa fa-chevron-left" aria-hidden="true"></i>',
			'nextText':'<i class="fa fa-chevron-right" aria-hidden="true"></i>',
		});

		// Muestra y esconde la barra lateral de las últimas recargas.
		$('.orangeMenu').click(function(){
			var $orangeMenu = $(this);
			var $orangeMenuArrow = $orangeMenu.find('#orangeMenuArrow');
			var $shoppingCart = $('.shoppingCartContent');

			if($orangeMenuArrow.hasClass('fa-angle-double-left')){
				$orangeMenu.animate({right: 250}, 200);
				$shoppingCart.show(200);
				$orangeMenuArrow.removeClass('fa-angle-double-left').addClass('fa-angle-double-right');
			}else{
				$orangeMenu.animate({right: 0}, 200);
				$shoppingCart.hide(200);
				$orangeMenuArrow.removeClass('fa-angle-double-right').addClass('fa-angle-double-left');
			}
		});

		$('.readMore').fancybox();

		$('#abroad').click(function(){
			if($('#divClientPhone').is(':visible')){
				// Escondo el número del cliente.
				$('#divClientPhone').slideUp(400, function(){
					// El número del cliente será el invitado.
					$('#clientPhone').val('1111111111');
				});
				// Escondo los números de la agenda.
				$('#frequentPurchasesDiv').hide();
				// Muestra el div del teléfono receptor.
				$('#recipientDiv').slideDown();
				// Desplazo la pantalla para que se vea de primero el número a recargar.
				$('html, body').animate({
					scrollTop: $('.phonePane').offset().top
				}, 1000);
				// Pongo el cursor en la entrada del número.
				$('#phoneInput').focus();
			}
		});

		$('#domestic').click(function(){
			if($('#divClientPhone').is(':visible')){
				// Escondo el número del cliente.
				$('#divClientPhone').slideUp(400, function(){
					// El número del cliente será el invitado.
					$('#clientPhone').val('1111111111');
				});
				// Escondo los números de la agenda.
				$('#frequentPurchasesDiv').hide();
				// Muestra el div del teléfono receptor.
				$('#recipientDiv').slideDown();
				// Desplazo la pantalla para que se vea de primero el número a recargar.
				$('html, body').animate({
					scrollTop: $('.phonePane').offset().top
				}, 1000);
				// Pongo el cursor en la entrada del número.
				$('#phoneInput').focus();
				// Pongo por defecto a USA en el listado.
				$('#phoneInput').intlTelInput('setCountry', 'us');
			}
		});

		$('#pinless').click(function(){
			if($('#divClientPhone').is(':visible')){
				// Escondo el número del cliente.
				$('#divClientPhone').slideUp(400, function(){
					// El número del cliente será el invitado.
					$('#clientPhone').val('1111111111');
				});
				// Escondo los números de la agenda.
				$('#frequentPurchasesDiv').hide();
				// Muestra el div del teléfono receptor.
				$('#recipientDiv').slideDown();
				// Desplazo la pantalla para que se vea de primero el número a recargar.
				$('html, body').animate({
					scrollTop: $('.phonePane').offset().top
				}, 1000);
				// Pongo el cursor en la entrada del número.
				$('#phoneInput').focus();
				// Pongo por defecto a USA en el listado.
				$('#phoneInput').intlTelInput('setCountry', 'us');
				// Escondo el botón de continuar.
				$('#continue').hide();
				// Avanzo al siguiente paso.
				addFundsStatus = 2;
				// Obtengo los productos de Estados Unidos.
				getProducts('us', function() {
					$('#product .productBox').each(function(index){
						if($(this).attr('data-isunlimited') == 1){
							$(this).removeClass('hide');
						}
					});
					$('.anotherCarrier').hide();
					// Oculta la pestaña de productos PIN.
					$('ul.tabs li:last').hide();
					// Muestra los tabs.
					$('#productDiv').fadeIn();
				});
			}
		});
		<?
		if(isset($status))
		{ ?>
			$('.fancybox').fancybox();

			// La tienda desea realizar otra recarga para este mismo número.
			$('#msg').on('click', '#startOver', {}, function(e){
				var clientPhone = $(this).attr('data-clientPhone');
				$('#clientPhone').val(clientPhone);
				showFrequentPurchases();
				// Desplazo la pantalla para que se vea de primero el número.
				$('html, body').animate({
					scrollTop: $('#addFunds').offset().top
				}, 1000);
			});<?
		} ?>
	});<?

	if(isset($status) && $status == 'Pending')
	{
		$confirmUrl = '';
		if(isset($provider))
		{
			if($provider == 'DOLLARPHO')
			{
				$confirmUrl = base_url('home/dollarPhoneConfirm/'.$id.'/'.$transId);
			}
			elseif($provider == 'CERETEL')
			{
				$confirmUrl = base_url('home/ceretelConfirm/'.$id.'/'.$transId);
			}
			elseif($provider == 'LOGICAL')
			{
				$confirmUrl = base_url('home/logicalConfirm/'.$id.'/'.$transId);
			}
			elseif($provider == 'PREPAYNAT')
			{
				$confirmUrl = base_url('home/prepayNationConfirm/'.$id);
			}
			elseif($provider == 'DPPINLESS')
			{
				$confirmUrl = base_url('home/getWebTransactionInfo/'.$transId.'/'.$id.'/'.$due.'/ajax');
			}
		} ?>
		// Determina el estado de la transacción.
		var status = '';
		var iteration = 0;
		var intervalId;

		// Hago un llamado continuo a la verificación del estado de la transacción.
		function checkStatus(){
			if(status != '' && status != 'Pending'){
				// Ya obtuve la respuesta final o espero si el estado aún es "pendiente".
				clearInterval(intervalId);
				return;
			}
			$.ajax({
				url: '<?= $confirmUrl; ?>',
				type: 'GET',
				dataType: 'json',
				success: function(item){
					status = item.status;

					switch(status){
						case 'Success':
							swal({
								title: '<?= lang('successful_topup'); ?>',
								text: '<?= lang('successful_topup2'); ?>',
								type: 'success',
								showCancelButton: false
							});
							// Selecciono el valor de la primera fila que es la más reciente.
							var curAmount = parseFloat($('.shoppingCartCategory strong').first().html());
							// Valido que esté en cero la última transacción para actualizar su valor.
							if(curAmount == 0)
							{
								// Actualizo el valor de la transacción.
								$('.shoppingCartCategory strong').first().html(parseFloat(item.amount).toFixed(2));
								$('.shoppingCartCategory span').first().html(item.phone + ' (Success)');
							}
							// Actualizo los totales así la última transacción no haya sido actualizada.
							var newTotal = parseFloat($('#productsCost').html()) + parseFloat(item.amount);
							$('#productsCost').html(newTotal);
							$('.shoppingCartTotal').html('$' + newTotal);
						case 'AlreadyApproved':
							// Muestro el mensaje.
							$('#msg .blue').fadeOut(400, function(){
								var message = item.message + ' <strong><a href="<?= base_url('printThermo/index/'.$id); ?>" data-fancybox-type="iframe" class="fancybox"><?= lang('print_receipt'); ?></a><strong>';
								$('#msg .blue').html(message);
								$('#msg .blue').fadeIn();
								$('#msg').append('<div class="clear10"></div><p><strong><?= lang('start_over'); ?> <?= $clientPhone; ?>? <a id="startOver" data-clientPhone="<?= $clientPhone; ?>"><?= lang('click_here'); ?></a></strong></p>');
							});
							break;
						case 'Pending':
							// Sigo esperando.
							break;
						case 'Error':
						case 'Failed':
							swal('<?= lang('failed_topup2'); ?>', item.error, 'error');
							$('#msg .blue').fadeOut(400, function(){
								$('#msg .blue').html(item.error);
								$('#msg').append('<div class="clear10"></div><p><strong><?= lang('start_over'); ?> <?= $clientPhone; ?>? <a id="startOver" data-clientPhone="<?= $clientPhone; ?>"><?= lang('click_here'); ?></a></strong></p>');
								$('#msg .blue').fadeIn();
							});
							break;
					}
				},
				error: function(xhr, status){
					console.log('<?= lang('something_failed'); ?>');
				}
			});
			iteration++;
			console.log('Iteration #' + iteration + ', has been ' + (iteration * 10) + ' seconds since first call.');
			if(iteration >= 12){
				// Ya pasaron 120 segundos sin recibir respuesta.
				$('#msg').html('<p class="error"><?= lang('msg_trans_still_pend'); ?><a href="<?= base_url('clientCtrl/transactionsList'); ?>"><?= lang('recent_transactions'); ?></a><?= lang('msg_trans_still_pend2'); ?></p>' +
					'<div class="clear10"></div><p><strong><?= lang('start_over'); ?> <?= $clientPhone; ?>? <a id="startOver" data-clientPhone="<?= $clientPhone; ?>"><?= lang('click_here'); ?></a></strong></p>');
				clearInterval(intervalId);
				return;
			}
		}

		// Espero 10 segundos para llamar la función que verifica el estado.
		intervalId = setInterval(checkStatus, 10000);<?
	} ?>
</script>