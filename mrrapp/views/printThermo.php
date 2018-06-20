<?php
$amountCalculated = ($transaction->includeCharge > 0) ? ($transaction->amount - $transaction->includeCharge) : $transaction->amount;
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>My Ring Ring</title>
		<style>
			*{box-sizing:border-box; padding:0; margin:0}
			body{margin:0; padding:10px 0; font-size:11px; font-family:verdana}
			.printContainer{width:100%; max-width:595px; margin:0 auto; padding:40px 0}
			h1{font-weight:normal; font-size:1em; text-transform:uppercase; text-align:center; border-top:3px #000 double; border-bottom:3px #000 double; padding:5px 0; margin:24px 0 10px 0}
			p{padding:0; margin:0;}
			.printTable{margin:24px 0 16px 0; clear:both;}
			.printTable th{text-align:right; font-weight:normal; padding:0 10px 0 0;}
			.printFooter{border-top:3px double #000; margin:36px 0 0 0; padding:24px 5px 0 5px}
			.printFooter td{width:33.333%;}
		</style>
	</head>
	<body>
		<div class="printContainer">
			<p align="center">
				<?= $_SESSION['userName']; ?><br>
				<?= $transaction->created; ?>
			</p>
			<h1>PAYMENT PROOF</h1>
			<p align="center">
				ELECTRONIC TOP-UP<br>
				My Ring Ring $<?= $amountCalculated; ?> USD
			</p>
			<table class="printTable" width="90%">
				<tr>
					<th>SUPPLIER:</th>
					<td><?= $product->name; ?></td>
				</tr>
				<tr>
					<th>REFERENCE:</th>
					<td><?= $transaction->id; ?></td>
				</tr>

				<tr>
					<th>TRANSACTION:</th>
					<td><?= $transaction->transId; ?></td>
				</tr>

				<?php if (isset($transaction->pin) && !empty($transaction->pin)): ?>

				<tr>
					<th>PIN:</th>
					<td><?php echo $transaction->pin; ?></td>
				</tr>

				<?php endif; ?>

				<tr>
					<th>PHONE:</th>
					<td><?= $transaction->phone; ?></td>
				</tr>
				<tr>
					<th>AMOUNT:</th>
					<td>$<?= $amountCalculated; ?> USD</td>
				</tr><?
				if($transaction->serviceCharge > 0)
				{
					$serviceChargeCalculated = $transaction->serviceCharge + (($transaction->includeCharge > 0) ? $transaction->includeCharge : 0); ?>
					<tr>
						<th>CHARGE FOR SERVICE:</th>
						<td>$<?= $serviceChargeCalculated; ?> USD</td>
					</tr><?
				}
				if($transaction->serviceCharge == 0 && $transaction->includeCharge > 0)
				{ ?>
					<tr>
						<th>CHARGE FOR SERVICE:</th>
						<td>$<?= $transaction->includeCharge; ?> USD</td>
					</tr><?
				} ?>

				<tr>
					<th>TOTAL:</th>
					<td>$<?= $transaction->total; ?> USD</td>
				</tr>

			</table>
			<p align="center">THANK YOU!</p>
			<h1 align="center">THIS TRANSACTION DOES NOT HAVE REFUND OF MONEY</h1>
		</div>
		<script type="text/javascript">
			window.print();
		</script>
	</body>
</html>
