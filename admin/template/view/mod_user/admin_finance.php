<?php if (!defined('IN_CONTEXT')) die('access violation error!'); ?>
<script type="text/javascript" language="javascript">
<!--
function check_validate() {
	var re = /^\d+(\.\d+)?$/;
	if(!re.test($("#usermoney_amount_").val())){
		alert('<?php _e('Please input Number');?>');
		$("#usermoney_amount_").val('');
	}
}

function on_success(response) {
    var o_result = _eval_json(response);
    if (!o_result) {
        return on_failure(response);
    }
    
    var stat = document.getElementById("adminusrfnc_stat");
    if (o_result.result == "ERROR") {
        stat.innerHTML = o_result.errmsg;
        return false;
    } else if (o_result.result == "OK") {
	    stat.innerHTML = "<?php _e('OK, refreshing...'); ?>";
	    reloadPage();
    } else {
        return on_failure(response);
    }
}

function on_failure(response) {
    document.getElementById("adminusrfnc_stat").innerHTML = "<?php _e('Request failed!'); ?>";
    return false;
}
//-->
</script>
<span id="adminusrfnc_stat" class="status" style="display:none;"></span>
<p style='margin-left:5px;margin-top:5px;'><a href='index.php?_m=mod_user&_a=admin_list'><?php _e('Back');?></a></p>
<table width="100%" border="0" cellspacing="1" cellpadding="2" style="line-height:24px;">
    <tbody>
        <tr>
            <td class="label"><?php _e('Total Saving'); ?></td>
            <td class="entry"><?php echo $curr_user_ext->total_saving; ?></td>
            <td class="label"><?php _e('Total Payment'); ?></td>
            <td class="entry"><?php echo $curr_user_ext->total_payment; ?></td>
        </tr>
        <tr>
            <td class="label"><?php _e('Balance'); ?></td>
            <td class="entry" colspan="3"><?php echo $curr_user_ext->balance; ?></td>
        </tr>
    </tbody>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="2" style="line-height:24px;">
<tr>
  <td width="10%"><b><?php _e('Operation'); ?></b></td>
</tr>
</table>
<?php
$userfnc_form = new Form('index.php', 'userfncform', 'check_fnc_info');
$userfnc_form->p_open('mod_user', 'admin_financialop', '_ajax');
?>
<table width="100%" border="0" cellspacing="1" cellpadding="2" style="line-height:24px;">
    <tfoot>
        <tr>
            <td colspan="4">
            <?php
            echo Html::input('reset', 'reset', __('Reset'));
            echo Html::input('submit', 'submit', __('Submit'));
            echo Html::input('hidden', 'u_id', $curr_user_ext->user_id);
            ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
        <tr>
            <td class="label"><?php _e('Amount'); ?></td>
            <td class="entry">
            <?php
            echo Html::input('text', 'usermoney[amount]', '0.00', 'onblur=check_validate();', 
                $userfnc_form, 'RequiredTextbox', 
                __('Please input amount!'));
            ?>
            </td>
            <td class="label"><?php _e('Type'); ?></td>
            <td class="entry">
            <?php
            echo Html::select('usermoney[type]', 
                array('1' => __('Saving'), '2' => __('Deduction')));
            ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php _e('Memo'); ?></td>
            <td class="entry" colspan="3">
            <?php echo Html::textarea('usermoney[memo]', '', 'rows="8" cols="54"'); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php
$userfnc_form->close();
$running_msg = __('Updating financial info...');
$custom_js = <<<JS
$("#adminusrfnc_stat").css({"display":"block"});
$("#adminusrfnc_stat").html("$running_msg");
_ajax_submit(thisForm, on_success, on_failure);
return false;

JS;
$userfnc_form->addCustValidationJs($custom_js);
$userfnc_form->writeValidateJs();
?>
<table width="100%" border="0" cellspacing="0" cellpadding="2" style="line-height:24px;">
<tr>
  <td width="10%"><b><?php _e('Transaction History'); ?></b></td>
</tr>
</table>
	
<table  width="100%" border="0" cellspacing="1" cellpadding="2" style="line-height:24px;" id="admin_userfinance_list">
	<thead>
		<tr>
            <td><?php _e('Time'); ?></td>
            <td><?php _e('Amount'); ?></td>
            <td><?php _e('Type'); ?></td>
            <td><?php _e('Memo'); ?></td>
        </tr>
    </thead>
    <tbody>
    <?php
    if (sizeof($transactions) > 0) {
        $row_idx = 0;
        foreach ($transactions as $transaction) {
    ?>
        <tr class="row_style_<?php echo $row_idx; ?>">
            <td><?php echo date('Y-m-d H:i:s', $transaction->action_time); ?></td>
        	<td><?php echo $transaction->amount; ?></td>
        	<td><?php echo Toolkit::switchText($transaction->type, 
                    array('1' => __('Saving'), '2' => __('Deduction'))); ?></td>
        	<td><?php echo $transaction->memo; ?></td>
        </tr>
    <?php
            $row_idx = 1 - $row_idx;
        }
    } else {
    ?>
    	<tr class="row_style_0">
    		<td colspan="4"><?php _e('No Records!'); ?></td>
    	</tr>
    <?php
    }
    ?>
    </tbody>
</table>
<?php
include_once(P_TPL.'/common/pager.php');
?>
