<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div>
<table cellpadding="5" style="border-collapse: collapse; width: 100%;" border="0">
<tbody>
    <tr>

        <td style="color: #000; font-size: 30px; text-align: left; width: 50%; vertical-align: top;">
            <img src="<?= base_url('images/logo-myringring.png'); ?>" width="60">
			<strong>MY Ring Ring</strong>
        </td>
        <td style="text-align: right; width: 50%;">
            <div><strong>Direct Reload</strong></div>
            <div>115 W Lincoln ave Suite #122</div>
            <div>Yakima, WA 98902</div>
            <div>Tel (509) 225 9057</div>
        </td>
    </tr>
</tbody>
</table>
<br />
<div style="width: 100%;">
    <div style="float: left; width: 50%;">
        <div style="text-align: center; width: 95.5%;">
		    <span style="font-size: 18px;">www.myringring.net<span>&nbsp;<strong style="color: #000; font-size: 20px;"><?= lang('invoice'); ?></strong>
        </div>
        <table cellpadding="5" style="border-collapse: collapse;" border="1" width="95.5%">
        <tbody>
            <tr>
                <td style="color: #000; text-align: center; width: 50%;"><?= lang('date'); ?></td>
                <td style="color: #000; text-align: center; width: 50%;"><?= lang('invoice_num'); ?></td>
            </tr>
            <tr>
                <td style="color: #000; text-align: center;"><?= $invoice_date; ?></td>
                <td style="color: #000; text-align: center;"><?= intval(microtime(TRUE)); ?></td>
            </tr>
        </tbody>
        </table>
    </div>
    <div style="float: right; width: 50%;">
        <table cellpadding="5" style="border-collapse: collapse; width: 100%;" border="1">
        <tbody>
            <tr>
                <td style="color: #000; text-align: center;">
                    <strong><?= lang('bill_to'); ?></strong>
                </td>
            </tr>
            <tr>
                <td style="color: #000; text-align: right;">

                    <?php if ($cdata): ?>

                    <div><?= $cdata->name; ?></div>
                    <div><?= $cdata->address; ?></div>
                    <div><?= $cdata->postal_code; ?></div>
                    <div>Tel: <?= $cdata->phone; ?></div>
                    <div>Contact: <?= $cdata->contactName; ?></div>

                    <?php endif; ?>

                </td>
            </tr>
        </tbody>
        </table>
    </div>
</div>
<br />
<br />
<table cellpadding="5" style="border-collapse: collapse; width: 100%;" border="1">
<thead>
    <tr>
        <th colspan="8" style="text-align: center;"><?= lang('title_date_range').' '.$from?></th>
    </tr>

    <tr>
        <th style="text-align: center;"><?= lang('col_date'); ?></th>
        <th style="text-align: center;"><?= lang('col_transId'); ?></th>
        <th style="text-align: center;" width="30%"><?= lang('col_product'); ?></th>
        <th style="text-align: center;"><?= lang('col_client_phone'); ?></th>
        <th style="text-align: center;"><?= lang('col_phone'); ?></th>
        <th style="text-align: center;"><?= lang('col_face_value'); ?></th>
        <th style="text-align: center;"><?= lang('col_comm'); ?></th>
        <th style="text-align: center;">Sub Total</th>
    </tr>
</thead>
<tbody>
	<? $facevalue_total = $commission_total = 0;?>
    <?php foreach($items as $item): ?>

    <tr style="font-weight: lighter">
        <th style="text-align: center;"><?= $item->created; ?></th>
        <th style="text-align: center;"><?= $item->transId; ?></th>
        <th style="text-align: center;" width="30%"><?= $item->product ?></th>
        <th style="text-align: center;"><?= $item->clientPhone; ?></th>
        <th style="text-align: center;"><?= $item->phone; ?></th>
        <th style="text-align: center;"><?= $item->amount; ?></th>
        <th style="text-align: center;"><?= $item->profit; ?></th>
        <th style="text-align: center;"><?= $item->total; ?></th>
    </tr>
	<?	$facevalue_total += $item->amount;
		$commission_total += $item->profit;?>
    <?php endforeach; ?>

    <tr>
        <td style="text-align: right;" colspan="5"><strong>Totals:</strong></td>
        <td style="text-align: right;"><strong><?= moneyFormat($facevalue_total); ?></strong></td>
        <td style="text-align: right;"><strong><?= moneyFormat($commission_total); ?></strong></td>
        <td style="text-align: right;"><strong><?= moneyFormat($facevalue_total - $commission_total); ?></strong></td>
    </tr>
</tbody>
</table>
