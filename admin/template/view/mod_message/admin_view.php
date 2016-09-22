<?php
if (!defined('IN_CONTEXT')) die('access violation error!');
?>
<style type="text/css">
.label {width:15%;}
</style>
<script type="text/javascript" language="javascript">
<!--
function backPrv(){
	window.location.href="index.php?_m=mod_message&_a=admin_list";	
}
//-->
</script>
<table id="downloadform_table" class="form_table" width="100%" border="0" cellspacing="0" cellpadding="0" style="line-height:24px;">
    <tfoot>
      <tr>
    	<td colspan="2">
        <?php echo Html::input('button', 'cancel', __('Cancel'), 'onclick="backPrv()"');?>
        </td>
      </tr>
    </tfoot>
    <tbody>
        <tr>
            <td class="label"><?php _e('Nickname'); ?></td>
            <td class="entry">
            <?php
				echo $message->username;
            ?>
            </td>
        </tr>
		<tr>
            <td class="label"><?php _e('E-mail'); ?></td>
            <td class="entry">
            <?php
				echo $message->email;
            ?>
            </td>
        </tr>
		<tr>
            <td class="label"><?php _e('Telephone'); ?></td>
            <td class="entry">
            <?php
				echo $message->tele;
            ?>
            </td>
        </tr>
		<tr>
            <td class="label"><?php _e('Message'); ?></td>
            <td class="entry">
            <?php
				echo htmlentities($message->message,ENT_COMPAT,"UTF-8");
            ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php _e('Create Time'); ?></td>
            <td class="entry">
            <?php
            	$create_time = date("Y-m-d H:i:s", $message->create_time);
            	echo $create_time;
            ?>
            </td>
        </tr>
    </tbody>
</table>
