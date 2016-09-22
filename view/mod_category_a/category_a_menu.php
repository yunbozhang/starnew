<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

if (!function_exists('showCategoryMenuA')) {
    function showCategoryMenuA(&$category_tree) {
        // TODO : Deal with article categoried list link in menu
        if(empty($category_tree)) $category_tree = array();
        foreach ($category_tree as $category) {
    ?>
    <li>
    <?php
            if (sizeof($category->slaves['ArticleCategory']) > 0) {
    ?>
    <a href="<?php echo Html::uriquery('mod_article', 'fullist', array('caa_id' => $category->id)); ?>">
    <?php echo $category->name; ?></a>
	<ul>
        <?php showCategoryMenuA($category->slaves['ArticleCategory']); ?>
    </ul>
    <?php
            } else {
    ?>
    <a href="<?php echo Html::uriquery('mod_article', 'fullist', array('caa_id' => $category->id)); ?>">
    <?php echo $category->name; ?></a>
    <?php
            }
    ?>
    </li>
    <?php
        }
    }
}
$id_seed = Toolkit::randomStr();
?>


<div class="list_main category">
	<div class="prod_type">
	<div id="pro_type_<?php echo $id_seed; ?>">
		<ul>
			<?php showCategoryMenuA($categories); ?>
			<div class="blankbar1"></div>
		</ul>
	</div>
	</div>
	<div class="list_bot"></div>
</div>
<div class="blankbar"></div>


<script type="text/javascript" language="javascript">
/**
 * for menu-drop type
 */
var type = "<?php echo trim(ParamHolder::get('article_category_type',''));?>";
if (type == 'click') {
	$(function(){
		$('#pro_type_<?php echo $id_seed; ?> li:has(ul)').click(function(event){
	    	if (this == event.target) {
	        	if ($(this).children().is(':hidden')) {
	                $(this).css({background:'url(<?php echo P_TPL_WEB; ?>/images/minus.gif) no-repeat left 13px','text-indent':'16px','padding-top':'3px'})
	                .children().show();
	                
	              	$(this).siblings().each(function (){
	              		$(this).find("ul").hide();
	              		if ($(this).children().is("ul")){
	              			$(this).css({background:'url(<?php echo P_TPL_WEB; ?>/images/plus.gif) no-repeat left 13px','text-indent':'16px','padding-top':'3px'});
	              	    }
	              	});
	            } else {
	                $(this).css({background:'url(<?php echo P_TPL_WEB; ?>/images/plus.gif) no-repeat left 13px','text-indent':'16px','padding-top':'3px'})
	                .find("ul").hide();
	            }  
	        }
		}).css('cursor','pointer');
	          
	    $('#pro_type_<?php echo $id_seed; ?> li:has(ul)').css({background:'url(<?php echo P_TPL_WEB; ?>/images/plus.gif) no-repeat left 13px','text-indent':'16px','padding-top':'3px'});
	        	
	    $('#pro_type_<?php echo $id_seed; ?> li:not(:has(ul))').css({cursor: 'pointer','list-style-image':'none'});
	});
} else {
	$("#pro_type_<?php echo $id_seed; ?> > ul").droppy();
	$("#pro_type_<?php echo $id_seed; ?> ul ul li:last-child").css("border","0px");
}
</script>
