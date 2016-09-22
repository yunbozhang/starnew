<?php 
if (!defined('IN_CONTEXT')) die('access violation error!');
?>

<!-- 页脚【start】 -->
<div id="footer">
	<div class="copyright">
	<style type="text/css">
		.mb_foot_block {position:relative;}
		.mb_foot_block a{display:inline;}
	</style>
	<?php if (Content::countModules('footer') > 0 || Toolkit::editMode()) Content::loadModules('footer'); ?><?php echo SITE_HAOSH.SERVICE53.WEB_ICP; ?>
	</div>
</div>
<?php Html::ddScript();
Html::includeJs('/picAutoZoom.js');

if (SessionHolder::get('page/status', 'view') == 'edit') {
	Html::includePopupJs();	//import jquery-ui library.
}
?>
<script type="text/javascript" language="javascript">
<!--
    $.ajaxSetup({
        timeout: 300000
    });
//-->
</script>
<?php
/*
 * 浮动客服
 * for sitestarv1.3
 */
if ((SessionHolder::get('page/status', 'view') == 'view') && (QQ_ONLINE == '1')) {
	if (isset($_SITE->s_locale)&&$_SITE->s_locale=='zh_CN') {
		if (QQ_ONLINE_POS == 'left') {
		$zxkfCss = <<<CSS
	.zxkf_head {background:url(images/head.gif) no-repeat;}
	.zxkf_obtn {margin-top:0;width:36px;background:url(images/left_bar.gif) no-repeat;float:left;height:155px;margin-right:-5px;_width:41px;}
CSS;
		} else {
			$zxkfCss = <<<CSS
	html {overflow-x:hidden;}
	.zxkf_head {background:url(images/r_head.gif) no-repeat;}
	.zxkf_obtn {margin-top:0;width:36px;background:url(images/right_bar.gif) no-repeat;float:left;height:155px;margin-right:-5px;_width:41px;}
CSS;
		}
	}else{//如果是英文
		if (QQ_ONLINE_POS == 'left') {
			$zxkfCss = <<<CSS
	.zxkf_head {background:url(images/head_en.gif) no-repeat;}
	.zxkf_obtn {margin-top:0;width:36px;background:url(images/left_bar_en.gif) no-repeat;float:left;height:155px;margin-right:-5px;_width:41px;}
CSS;
		} else {
			$zxkfCss = <<<CSS
	html {overflow-x:hidden;}
	.zxkf_head {background:url(images/r_head_en.gif) no-repeat;}
	.zxkf_obtn {margin-top:0;width:36px;background:url(images/right_bar_en.gif) no-repeat;float:left;height:155px;margin-right:-5px;_width:41px;}
CSS;
		}
	}

echo <<<CSS
<style type="text/css">
{$zxkfCss}
.zxkf_info {padding-bottom:10px;padding-left:0px;padding-right:0px;background:url(images/service_bg.gif) repeat-y;width:145px;}
.down_kefu {width:145px;background:url(images/bottom.gif) no-repeat;height:10px;}
.zxkf_qqtable {background:url(images/s_c_bg.gif) repeat-x;}
.zxkf_qqtable span {padding-bottom:5px;line-height:20px;padding-left:0px;width:100px;padding-right:0px;font-size:13px;font-weight:bold;padding-top:5px;color: #ff6600;}
.zxkf_qqtable a {text-decoration:none;color:#FF6600;}
.zxkf_qqtable a:hover {text-decoration:none;color:#FF6600;}
.zxkf_qqtable A, .zxkf_qqtable A:visited {text-decoration:none;color:#ff6600;}
.zxkf_qqtable A:hover {text-decoration:none}
</style>
CSS;
// online list
$where = " s_locale = '".SessionHolder::get('_LOCALE')."'";
$online_list = new OnlineQq();
$onlines =& $online_list->findAll($where." and published='1' order by category asc");
 $currentlanguage=SessionHolder::get('_LOCALE');
 
 		if(QQ_ONLINE_TITLE){
			$QQ_ONLINE_TITLE_temp=unserialize(QQ_ONLINE_TITLE);
		}else{
			$QQ_ONLINE_TITLE_temp=array();
		}
		if (isset($QQ_ONLINE_TITLE_temp[SessionHolder::get('_LOCALE')]) ){
			$qqstitle= $QQ_ONLINE_TITLE_temp[SessionHolder::get('_LOCALE')];
		 }else{
			$qqstitle='';
		 }
?>
<div id="online_sevice"  onmouseover="toBig()" onmouseout="toSmall()">
<?php if (QQ_ONLINE_POS == 'right') {?><div class="zxkf_obtn"></div><?php }?><table style="float:left;" border="0" cellspacing="0" cellpadding="0" width="145">
  <tbody>
  <tr>
    <td class="zxkf_head" height="35" valign="top">&nbsp;</td></tr>
  <tr>
    <td class="zxkf_info" valign="top" align="center">
      <table class="zxkf_qqtable" border="0" cellspacing="0" cellpadding="0" width="135" align="center">
        <tbody>
        <tr>
          <td align="center" valign="top">
    	  <table width="108" border="0" align="center" cellpadding="0" cellspacing="0">
          <tr>
            <td height="10"></td>
          </tr>
          <tr>
            <td valign="top" align="center"><?php echo $qqstitle;?></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
        <?php
    	foreach ($onlines as $online) {
			
    		switch ($online->category) {
    			case '0': // QQ
    	?>
    	<tr>
          <td height="30" align="center"><span><img src="http://wpa.qq.com/pa?p=4:<?php echo $online->account; ?>:4" align="absMiddle" alt="QQ" border="0" />&nbsp;&nbsp;<a href="tencent://message/?uin=<?php echo $online->account; ?>&amp;Menu=yes" title="<?php echo $online->account; ?>" target="blank"><?php echo $online->qqname; ?></a></span></td></tr>
    	<?php
    				break;
    			case '1': // MSN
        ?>
        <tr>
          <td height="30" align="center"><span><img src="<?php echo P_TPL_WEB; ?>/images/MSN.jpg" alt="MSN" />&nbsp;&nbsp;<a href="msnim:chat?contact=<?php echo $online->account; ?>" title="<?php echo $online->account; ?>"><?php echo $online->qqname; ?></a></span></td></tr>
        <?php 
        			break;
        		case '2': // WangWang
        ?>
        <tr>
          <td height="30" align="center"><span><a href="http://amos.im.alisoft.com/msg.aw?v=2&amp;uid=<?php echo $online->account; ?>&amp;site=cntaobao&amp;s=1&amp;charset=utf-8" title="<?php _e('WangWang'); ?>(<?php echo $online->account; ?>)" target="_blank"><img src="http://amos.im.alisoft.com/online.aw?v=2&amp;uid=<?php echo $online->account; ?>&amp;site=cntaobao&amp;s=1&amp;charset=utf-8" border="0" /></a></span></td></tr>
        <?php
        			break;
        		case '3': // Skype
        ?>
        <tr>
          <td height="30" align="center"><span><a href="callto://<?php echo $online->account; ?>" title="Skype Me(<?php echo $online->account; ?>)" target="_blank"><img border="0" src="http://goodies.skype.com/graphics/skypeme_btn_small_green.gif" /></a></span></td></tr>
		<?php 
					break;
			}
		}
		?></table>          <span></span></td></tr>
</tbody></table></td></tr>
  <tr>
    <td class="down_kefu" valign="top"></td></tr></tbody></table>
<?php if (QQ_ONLINE_POS == 'left') {?><div class="zxkf_obtn"></div><?php }?></div>
<?php
	if (QQ_ONLINE_POS == 'left') {
?>
<script language="javascript">
lastScrollY=0;var InterTime=1;var maxWidth=-1;var minWidth=-152;var numInter=8;var BigInter;var SmallInter;var o=document.getElementById("online_sevice");var i=parseInt(o.style.left);
zxkf = function(id,_top,_left){var me=id.charAt?document.getElementById(id):id,d1=document.body,d2=document.documentElement;d1.style.height=d2.style.height='100%';me.style.top=_top?_top+'px':0;me.style.left=_left+"px";me.style.position='absolute';setInterval(function(){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));return arguments.callee;};
$(function(){zxkf('online_sevice',100,-152);});
function Big(){if(parseInt(o.style.left)<maxWidth){i=parseInt(o.style.left);i+=numInter;o.style.left=i+"px";if(i==maxWidth) clearInterval(BigInter);}}
function toBig(){clearInterval(SmallInter);clearInterval(BigInter);BigInter=setInterval(Big,InterTime);}
function Small(){if(parseInt(o.style.left)>minWidth){i=parseInt(o.style.left);i-=numInter;o.style.left=i+"px";if(i==minWidth)clearInterval(SmallInter);}}
function toSmall(){clearInterval(SmallInter);clearInterval(BigInter);SmallInter=setInterval(Small,InterTime);}
</script>
<?php 
	} else {
?>
<script language="javascript">
lastScrollY=0; var InterTime=1;var maxWidth=-1;var minWidth=-152;var numInter=8;var BigInter;var SmallInter;var o=document.getElementById("online_sevice");var i=parseInt(o.style.right);
zxkf = function (id,_top,_right){var me=id.charAt?document.getElementById(id):id,d1=document.body,d2=document.documentElement;d1.style.height=d2.style.height='100%';me.style.top=_top?_top+'px':0;me.style.right=_right+"px";me.style.position='absolute';setInterval(function(){me.style.top=parseInt(me.style.top)+(Math.max(d1.scrollTop,d2.scrollTop)+_top-parseInt(me.style.top))*0.1+'px';},10+parseInt(Math.random()*20));return arguments.callee;};
$(function(){zxkf('online_sevice',100,-152);});
function Big(){if(parseInt(o.style.right)<maxWidth){i=parseInt(o.style.right);i+=numInter;o.style.right=i+"px";if(i==maxWidth) clearInterval(BigInter);}}
function toBig(){clearInterval(SmallInter);clearInterval(BigInter);BigInter=setInterval(Big,InterTime);}
function Small(){if(parseInt(o.style.right)>minWidth){i=parseInt(o.style.right);i-=numInter;o.style.right=i+"px";if(i==minWidth) clearInterval(SmallInter);}}
function toSmall(){clearInterval(SmallInter);clearInterval(BigInter);SmallInter=setInterval(Small,InterTime);}
</script>
<?php
	}
}
?>
<!-- 页脚【end】 -->
</body>
</html>