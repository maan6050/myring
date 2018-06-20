<?php
defined('BASEPATH') OR exit('No direct script access allowed');

		if($_SESSION['userType'] == STORE || $_SESSION['userType'] == CUSTOMER)
		{ ?>
			<div class="clear10"></div>
			<div class="footer">
				<p><?= lang('customer_service'); ?> <strong>1 888 8137485</strong></p>
				<p><small>All rights reserved &copy;</small></p>
			</div><?
		} ?>
	</body>
</html>