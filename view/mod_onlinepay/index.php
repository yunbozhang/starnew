<?php if (!defined('IN_CONTEXT')) die('access violation error!'); ?>


<div class="art_list">
	<div class="art_list_title"><?php _e('Online Payment'); ?></div>
<?php
$prepay_form = new Form('index.php?_m=mod_onlinepay', 'prepayform', 'check_prepay_info');
$prepay_form->p_open('mod_onlinepay', 'do_payment');
?>
<?php echo Html::input('hidden', 'o_id', $curr_order->id); ?>
		<?php //unset($payaccts[6]);?>
		<div class="order_1"><?php _e('Select Gateway'); ?></div><div class="order_2"><?php
            echo Html::select('paygate', $payaccts, '', '', 
                $prepay_form, 'RequiredSelect', 
                __('Please select payment gateway!'));
            ?></div><div class="blankbar1"></div>
		<div class="order_1"><?php _e('Total Payment'); ?></div><div class="order_2"><?php echo CURRENCY_SIGN; ?><?php echo $curr_order->total_amount; ?></div><div class="blankbar1"></div>
<?php echo Html::input('submit', 'submit', __('Confirm & Pay Now'), 'class="submit_order orange"'); ?>
<?php
$custom_js = isset($custom_js)?$custom_js:'';
$prepay_form->close();
$prepay_form->addCustValidationJs($custom_js);
$prepay_form->writeValidateJs();
?>
</div>
