<div>
    <?php echo lang('chosen_language'); ?>&nbsp;<span class="fontBold orange"><?php echo lang('language_' . $language); ?></span>
</div>
<br />
<br />

<?php $other_lang = ('en' === $language ? 'es' : 'en'); ?>

<form id="swich_language_form" 
      class="noPadding" 
      name="swich_language_form" 
      action="<?php echo base_url('pinlessAdminCtrl/switchLanguage'); ?>" 
      method="GET">
    <input name="code" type="hidden" value="<?php echo $code; ?>" />
    <input name="ani" type="hidden" value="<?php echo $ani; ?>" />
    <input name="language" type="hidden" value="<?php echo $other_lang; ?>" />
    <input id="swich_language_btn" 
           type="button" 
           value="<?php echo lang('switch_to') . ' ' . lang('language_' . $other_lang); ?>" />
</form>
