<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="centeredContent adminPage">
	<h1><?= $title; ?></h1>
	<div class="breadcrumbs">
		<a href="<?= base_url('home'); ?>"><?= lang('home'); ?></a> / <?= $title; ?>
	</div>
	<div class="clear"></div>
	<div class="formContainer">
		<div class="fcHeader borderBottom">
			<h3><?= $title; ?></h3>
			<div class="clear"></div>
		</div>
		<div class="searchDiv">
			<form name="search" id="search" action="<?= base_url($controller.'/transactionsList'); ?>" method="post">
				<input type="date" id="from" name="from" value="<?= $from; ?>" placeholder="">
				<input type="date" id="to" name="to" value="<?= $to; ?>" placeholder="">
				<select id="status" name="status">
					<option value=""><?= lang('option_all'); ?></option>
					<option value="Success"<? if($status == 'Success') echo ' selected'; ?>><?= lang('option_success'); ?></option>
					<option value="Failed"<? if($status == 'Failed') echo ' selected'; ?>><?= lang('option_failed'); ?></option>
					<option value="Pending"<? if($status == 'Pending') echo ' selected'; ?>><?= lang('option_pending'); ?></option>
					<option value="Refunded"<? if($status == 'Refunded') echo ' selected'; ?>><?= lang('option_refunded'); ?></option>
				</select><?
				if(count($clients) > 0)
				{ ?>
					<select id="client" name="client">
						<option value=""><?= lang('option_all'); ?></option><?
						foreach($clients as $c)
						{ ?>
							<option value="<?= $c->id; ?>"<? if($c->id == $client) echo ' selected'; ?>><?= $c->name; ?></option><?
						} ?>
					</select><?
				} ?>
				<input type="submit" value="<?= lang('search'); ?>">
			</form>
		</div>
		<table>
			<thead>
				<tr><th><?= lang('col_date'); ?></th><? if($_SESSION['userType'] != STORE){ ?><th>Store</th><? } ?><th><?= lang('col_phone'); ?></th><th><?= lang('col_product'); ?></th><th><?= lang('col_total'); ?></th><th><?= lang('col_due'); ?></th><th><?= lang('col_fee'); ?></th><th><?= lang('col_transid'); ?></th><th><?= lang('col_status'); ?></th><th>&nbsp;</th></tr>
			</thead>
			<tbody><?
				if(count($transactions) > 0)
				{
					foreach($transactions as $t)
					{ ?>
						<tr>
							<td><?= $t->created; ?></td><?
							if($_SESSION['userType'] != STORE)
							{ ?>
								<td><?= $t->name; ?></td><?
							} ?>
							<td>
								<?= $t->phone; ?>
								<?= empty($t->pin) ? '' : '<br><strong>PIN:</strong> '.$t->pin; ?>
							</td>
							<td><?= $t->product; ?></td>
							<td align="right"><?= $t->amount; ?></td>
							<td align="right"><?= $t->balance; ?></td>
							<td align="right"><?= $t->profit; ?></td>
							<td align="center"><?= $t->transId; ?></td>
							<td id="td<?= $t->id; ?>">
								<?= $t->status; ?><?
								if($t->status == 'Pending')
								{ ?>
									<br><em><span id="c<?= $t->id; ?>" class="hide"><strong><?= lang('checking'); ?></strong></span>
										<a id="<?= $t->id; ?>" data-id="<?= $t->transId; ?>" data-due="<?= $t->balance; ?>" data-provider="<?= $t->providerId; ?>" class="checkStatus"><?= lang('check_status'); ?></a></em><?
								} ?>
							</td>
							<td><?
								if($t->status == 'Success')
								{ ?>
									<a href="<?= base_url('printThermo/index/'.$t->id); ?>" data-fancybox-type="iframe" class="fancybox"><?= lang('ticket'); ?></a><?
									if($t->providerId === 'LOGICAL')
									{ ?>
										<div class="clear10"></div>
										<a href="<?= base_url('clientCtrl/refundPinless/'.$t->id); ?>"><?= lang('refund'); ?></a><?
									}
									elseif($t->providerId == 'DPPINLESS')
									{ ?>
										<div class="clear10"></div>
										<a class="cancelOrderDPP" data-id="<?= $t->id; ?>"><?= lang('cancel'); ?></a><?
									}
								}
								if($t->status == 'PendingCO')
								{ ?>
									<a class="checkCO" data-id="<?= $t->id; ?>"><?= lang('check_status'); ?></a><?
								}
								if($_SESSION['userType'] == ADMIN)
								{ ?>
									<div class="clear10"></div>
									<a href="#" data-id="<?= $t->id; ?>" data-phone="<?= $t->phone; ?>" class="deleteItem">Delete</a><?
								} ?>
							</td>
						</tr><?
					}
				}
				else
				{ ?>
					<tr><td colspan="10"><?= lang('no_trans_found'); ?></td></tr><?
				} ?>
				<tr>
					<th colspan="<?= $_SESSION['userType'] != STORE ? '4' : '3'; ?>">Total &mdash; <em><?= count($transactions); ?> transactions</em></th><th align="right"><?= $totalAmount; ?></th><th align="right"><?= $totalDue; ?></th><th align="right"><?= $totalFee; ?></th><th colspan="3"></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script type="text/javascript"><?
	if(isset($msg))
	{ ?>
		alert('<?= $msg; ?>'); <?
	} ?>

	jQuery(document).ready(function($){
		$('.fancybox').fancybox();

		$('.checkStatus').click(function(){
			var id = $(this).attr('id');
			var provider = $(this).attr('data-provider');
			var transId = $(this).attr('data-id');
			var due = $(this).attr('data-due');

			var confirmUrl = '';
			switch(provider)
			{
				case 'CERETEL':
					confirmUrl = '<?= base_url('home/ceretelConfirm/'); ?>' + id + '/' + transId;
					break;
				case 'LOGICAL':
					confirmUrl = '<?= base_url('home/logicalConfirm/'); ?>' + id + '/' + transId;
					break;
				case 'PREPAYNAT':
					confirmUrl = '<?= base_url('home/prepayNationConfirm/'); ?>' + id;
					break;
				case 'DPPINLESS':
					confirmUrl = '<?= base_url('home/getWebTransactionInfo/'); ?>' + transId + '/' + id + '/' + due + '/ajax';
					break;
				default:
					confirmUrl = '<?= base_url('home/dollarPhoneConfirm/'); ?>' + id + '/' + transId;
			}

			$(this).fadeOut(400, function(){
				$('#c' + id).fadeIn();
			});

			$.ajax({
				// data-id => transId; id => transactionId.
				url: confirmUrl,
				type: 'GET',
				dataType: 'json',
				success: function(item){
					switch(item.status){
						case 'Success':
						case 'AlreadyApproved':
							alert(item.message);
							// Borro todo lo que hay en la celda del estado y pongo "Éxito".
							$('#td' + id).fadeOut(400, function(){
								$('#td' + id).html('Success');
								$('#td' + id).fadeIn();
							});
							break;
						case 'Pending':
						case 'Error':
							alert(item.error);
							// Escondo el letrero de "Verificando" y vuelvo a mostrar el enlace para verificar.
							$('#c' + id).fadeOut(400, function(){
								$('#' + id).fadeIn();
							});
							break;
						case 'Failed':
							alert(item.error);
							// Borro todo lo que hay en la celda del estado y pongo "Falló".
							$('#td' + id).fadeOut(400, function(){
								$('#td' + id).html('Failed');
								$('#td' + id).fadeIn();
							});
							break;
					}
				},
				error: function(xhr, status){
					alert('Something failed. Please reload the page and try again.');
				}
			});
		});

		$(document).on('click', '.deleteItem', function(){
			if(confirm('Are you sure you want to remove the top-up to the phone ' + $(this).attr('data-phone') + '?')){
				window.location = '<?= base_url('admin/transactionsList/'); ?>' + $(this).attr('data-id') + '/' + $(this).attr('data-phone');
			}
		});

		$('.cancelOrderDPP').click(function(){
			var orderId = $(this).data('id');
			if(orderId != null)
			{
				$.ajax({
					url: '<?= base_url('clientCtrl/cancelOrder/')?>' + orderId,
					type: 'GET',
					dataType: 'json',
					success: function(result){
						switch (result.status)
						{
							case 'Success':
								alert(result.msg);
								location.reload();
								break;
							default:
								alert(result.msg);
						}
                    },
					error: function(xhr, status){
						alert('Something failed. Please reload the page and try again.');
					}
				});
			}

		});

		$('.checkCO').click(function(){
			var orderId = $(this).data('id');
			if(orderId != null)
			{
				$.ajax({
					url: '<?= base_url('clientCtrl/getWebTransactionInfo/')?>' + orderId + '/ajax',
					type: 'GET',
					dataType: 'json',
					success: function(result){
						switch (result.status)
						{
							case 'Success':
								alert(result.msg);
								location.reload();
								break;
							default:
								alert(result.msg);
						}
                    },
					error: function(xhr, status){
						alert('Something failed. Please reload the page and try again.');
					}
				});
			}

		});
	});
</script>
