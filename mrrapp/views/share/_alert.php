<?php if (isset($alert_text) && !empty($alert_text) && isset($alert_type) && !empty($alert_type)): ?>

<div class="alert alert-dismissible alert-<?= $alert_type; ?>" role="alert">
    <?= $alert_text; ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
</div>

<?php endif; ?>