<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

?>
<script type="text/javascript">
<!-- 
function submitValue()
{
	var has_data = $(":radio:checked").attr("value");
	var tpl_name = $("#tpl_name").val();
	var remote_tpl = $("#is_remote").val();
	var tpl_id = $("#tpl_id").val();
	if(!has_data) 
	{
		alert('请选择模板安装方式');
		return;
	}
	else
	{
		if(has_data == 1){
			if (!confirm("<?php _e('This operation will clear the current system data, suggest you ready to current data backup.  Please carefully!');?>")) return;
	        if (!confirm("<?php _e('From the server will now get the initial data template, might take a few minutes. Please wait.');?>")) return;
	        parent.$("#loading_overlay").css("display", "block");
	        parent.$("#loading_anim").css("display", "block");
	        window.location.href="index.php?_m=mod_template&_a=admin_install_template&_r=_ajax&tpl_id="+tpl_id+"&is_remote="+remote_tpl+"&tpl_name="+tpl_name+"&has_data="+has_data;
		} else {
			if (!confirm("<?php _e('This operation will obtain the template files from server. Please wait!');?>")) return;
			if (!confirm("<?php _e('After switching template,some style of contents in your pages would be change.');?>")) return;
		parent.$("#loading_overlay").css("display", "block");
        parent.$("#loading_anim").css("display", "block");
		window.location.href="index.php?_m=mod_template&_a=admin_install_template&_r=_ajax&tpl_id="+tpl_id+"&is_remote="+remote_tpl+"&tpl_name="+tpl_name+"&has_data="+has_data;
		/*
        $.ajax({
        	timeout: 300000,
        	type:"GET",
        	url:"index.php?_m=mod_template&_a=admin_install_template&_r=_ajax&tpl_id="+tpl_id+"&is_remote="+remote_tpl+"&tpl_name="+tpl_name+"&has_data="+has_data,
        	success:function(msg){
				alert(msg);
				top.location.reload();
        	},
        	error:function(msg){
        		alert(msg);
				top.location.reload();
        	}
        });
		*/
		}
	}
}

function closeWindow()
{
	parent.tb_remove();
}
//-->
</script>
<div style="height: 80px; margin: 0px auto;margin-top:5px;">
        <table style="display:inline;float:left;height:70px;margin:10px 20px;" cellspacing="0" cellpadding="0" border="0" id="loginbox">
            <tbody>
	            <!--此段屏闭从服务器下载数据<tr style="height: 30px;">
	                <td style="border-bottom:1px dashed #dee6ef;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family:Arial, Helvetica;color:#588ac7;font-weight:12px;"><b style="*margin-left:24px;">1.</b></span>&nbsp;&nbsp;<input type="radio" name="has_data" id="has_data" value="1" /></td>
	                <td style="width:380px;border-bottom:1px dashed #dee6ef;font-size:12px;font-family:宋体;color:#f7580a;";>&nbsp;&nbsp;<?php _e('Installation template and experience data');?><span style="margin-left:22px;font-size:13px;color:red;"><?php _e('Choosing this item,your current data will be coverage.Be carefule!');?></span></td>
	            </tr>-->
	            <tr style="height:40px;border-bottom:1px dashed #dee6ef;">
	                <td style="border-bottom:1px dashed #dee6ef;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-family:Arial, Helvetica;color:#588ac7;font-weight:12px;"><b style="*margin-left:24px;"></b></span>&nbsp;&nbsp;<input type="radio" name="has_data" id="has_data" value="0" /></td>
	                <td style="width:380px;border-bottom:1px dashed #dee6ef;font-size:12px;font-family:宋体;color:#f7580a;">&nbsp;&nbsp;<?php _e('Only installation template');?></td>
	            </tr>
        	</tbody>
        </table>
        <input id="tpl_name" type="hidden" name="tpl_name" value="<?php echo $tpl_name;?>"/>
        <input id="is_remote" type="hidden" name="is_remote" value="<?php echo $remote_tpl;?>"/>
        <input id="tpl_id" type="hidden" name="tpl_id" value="<?php echo $tpl_id;?>"/>
        <div style="height:25px;line-height:25px;margin:0 auto;text-align:center;width:200px;margin-top:5px;position:relative;right:28px;*right:25px;;">
        	<span onmouseover="$(this).css('cursor','pointer');" onmouseout="$(this).css('cursor','default');" style="padding-top:2px;font-family:宋体;font-size:15px;float:left;width:80px;height:28px;background:url('template/images/button_background.png');" onclick="submitValue();"><span><?php _e('Ok');?></span></span>
        	<span onmouseover="$(this).css('cursor','pointer');" onmouseout="$(this).css('cursor','default');" style="padding-top:2px;font-family:宋体;font-size:15px;float:right;width:80px;height:28px;background:url('template/images/button_background.png');" onclick="closeWindow();"><?php _e('Cancel');?></span>
        </div>
</div>
