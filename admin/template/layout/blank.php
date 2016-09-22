<?php
if (!defined('IN_CONTEXT')) die('access violation error!');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php _e('charset'); ?>" />
<title>网站系统管理</title><?php if( isset($tplcss) ){?>
<link rel="stylesheet" type="text/css" href="<?php echo P_TPL_WEB; ?>/css/<?php echo $tplcss;?>.css" />
<?php
}
if ((!ToolKit::getCorp() || !Toolkit::getAgent()))	{
?>
<link rel="stylesheet" type="text/css" href="<?php echo P_TPL_WEB; ?>/css/style.css" />
<script type="text/javascript" language="javascript" src="../script/popup/jquery-1.4.3.min.js"></script>
<script type="text/javascript" language="javascript" src="../script/helper.js"></script>
<style type="text/css">
html,body {background-color:#CCD8EF;}
.fr {background-color:#FFF;}
.label {width:15%;}
</style>
<?php } else { ?><!-- Overlay style -->
<style type="text/css">
<!--
html,body {background-color:#FFF;}
-->
</style><?php }?>
<!-- // Overlay style -->
</head>

<body>
	
	<div id="wrap">
		
		<div class="fr">
			<?php include_once($_content_); ?>
		</div>
	
	</div>
</body>
</html>