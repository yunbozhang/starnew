<?php
if (!defined('IN_CONTEXT')) die('access violation error!');
if(!isset($curr_friendlink)) $curr_friendlink=new Friendlink();
?>
<div class="status_bar">
<?php if (Notice::get('mod_friendlink/msg')) { ?>
	<span id="adminflfrm_stat" class="status"><?php echo Notice::get('mod_friendlink/msg'); ?></span>
<?php } ?>
</div>
<script type="text/javascript" language="javascript">

function hidden_radio(val){
	if(val==2){
		document.getElementById("friendlink_text").style.display="";
		document.getElementById("friendlink_img").style.display="none";
	}else{
		document.getElementById("friendlink_text").style.display="";
		document.getElementById("friendlink_img").style.display="";
	}
}
function check_fl_info(obj){
	var radios = obj.elements["fl_type"];
	var val = 0;
	for ( var i = 0; i < radios.length; i++) {
	  if (radios[i].checked==true) {
		  val=radios[i].value;
	  }
	}
	if(val==1){
		if (/^\s*$/.test(obj.elements["fl_file"].value))
		{
			alert("请选择要上传的链接图片！");
			obj.elements["fl_file"].focus();
			return false;
		}

	}
	if (/^\s*$/.test(obj.elements["friendlink[fl_name]"].value))
		{
			alert("请输入友情链接的名称！");
			obj.elements["friendlink[fl_name]"].focus();
			return false;
		}
	return true;

}

function backPrv(){
	window.location.href="index.php?_m=mod_friendlink&_a=admin_list";	
}
</script>
<?php
$friendlink_form = new Form('index.php', 'friendlinkform', 'check_fl_info');
$friendlink_form->setEncType('multipart/form-data');
$friendlink_form->p_open('mod_friendlink', $next_action);
?>

<table id="friendlinkform_table" class="form_table" width="100%" border="0" cellpadding="2" cellspacing="0" style="line-height:24px;">
    <tfoot>
        <tr>
            <td colspan="2">
            <?php
			echo Html::input('button', 'cancel', __('Cancel'), 'onclick="backPrv();"');
            echo Html::input('reset', 'reset', __('Reset'));
            echo Html::input('submit', 'submit', __('Save'));
            echo Html::input('hidden', 'friendlink[id]',isset($curr_friendlink->id)? $curr_friendlink->id:'');
            ?>
            </td>
        </tr>
    </tfoot>
    <tbody>
		<tr>
            <td class="label"><?php _e('Link Image'); ?></td>
            <td class="entry">
            <?php
            echo Html::input('radio', 'fl_type', '1', 
                ' onclick="hidden_radio(1)" checked');
            ?>：<?php _e('image link');?>
			
			<?php
            echo Html::input('radio', 'fl_type', '2', 
                'onclick="hidden_radio(2)"');
            ?>：<?php _e('text link');?>
            </td>
        </tr>
        <tr id="friendlink_img" style="display:">
            <td class="label"><?php _e('Link Image'); ?></td>
            <td class="entry">
            <?php
            echo Html::input('file', 'fl_file', '', 
                '');
            ?>
			<BR />
			<?php _e('Supported file format'); ?>:<?php echo PIC_ALLOW_EXT;?>
			<BR />
			<?php _e('Upload size limit'); ?>:<?php echo ini_get('upload_max_filesize');?>
            </td>
        </tr>
        <tr id="friendlink_text" style="display:">
            <td class="label"><?php _e('Name'); ?></td>
            <td class="entry">
            <?php
            echo Html::input('text', 'friendlink[fl_name]', isset ($curr_friendlink->fl_name)?$curr_friendlink->fl_name:'', 
                'class="textinput"');
            ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php _e('Link Addr'); ?></td>
            <td class="entry">
            <?php
            //echo Html::input('text', 'friendlink[fl_addr]', $curr_friendlink->fl_addr, 
            echo Html::input('text', 'friendlink[fl_addr]', 'http://', 
                'class="textinput"');
            ?>
            </td>
        </tr>
        <tr>
            <td class="label"><?php _e('Language'); ?></td>
            <td class="entry">
            <?php
            echo Toolkit::switchText(isset($curr_friendlink->s_locale)?$curr_friendlink->s_locale:$mod_locale, 
                Toolkit::toSelectArray($langs, 'locale', 'name'));
            echo Html::input('hidden', 'friendlink[s_locale]', 
           		isset($curr_friendlink->s_locale)?$curr_friendlink->s_locale:$mod_locale);
            ?>
            </td>
        </tr>
        <tr>
            <td class="label"></td>
            <td class="entry">
            <?php
            echo Html::input('checkbox', 'ismemonly', '1');
            ?>
            &nbsp;<?php _e('Member only access'); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php
$friendlink_form->close();
?>