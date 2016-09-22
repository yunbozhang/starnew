<?php


if (!defined('IN_CONTEXT')) die('access violation error!');

/**
 * Module block object
 * 
 */
class ModuleBlock extends RecordObject {
	public static $cache_count_data;
	public static $cache_select_data;
    
    public static $cache_count_handle = true;
    public static $cache_select_handle = true;
    
    public static function cacheStrategy($param1,$param2)
    {
    	if((TABLE_CACHE == 1) && (SessionHolder::get('page/status') == 'view') && empty($_GET['_v']))
    	{
    		$prefix = Config::$tbl_prefix;
    		$getObjects = array();
    		$objects = array();
    		
    		if(strcmp($param1,"SELECT * FROM `{$prefix}module_blocks` WHERE `s_pos` =? AND (`s_locale`=? OR `s_locale`='_ALL') AND (`s_query_hash`=? OR `s_query_hash`='_ALL') AND for_roles LIKE ? AND (`module`<>? OR `action`<>?) ORDER BY `i_order`") == 0)
    		{
    			if(empty(self::$cache_select_data))
    			{
    				if(self::$cache_select_handle)
    				{
    					self::$cache_select_handle = false;
    					$db = MysqlConnection::get();
    					$sql1 = "SELECT * FROM `{$prefix}module_blocks` WHERE (`s_locale`='{$param2[1]}' OR `s_locale`='_ALL') AND (`s_query_hash`='{$param2[2]}' OR `s_query_hash`='_ALL') AND for_roles LIKE '{$param2[3]}' AND (`module`<>'{$param2[4]}' OR `action`<>'{$param2[5]}') ORDER BY `i_order`";
    					$rs =& $db->query($sql1);
    					$objects =& $rs->fetchObjects('ModuleBlock',array(false, false));
    					
    					self::$cache_select_data = $objects;
    					$rs->free();
    					
    					foreach($objects as $v)
    					{
    						if($v->s_pos == $param2[0])
    						{
    							$getObjects[] = $v;
    						}
    					}
    				}
    			}
    			else
    			{
    				foreach(self::$cache_select_data as $v)
    				{
    					if($v->s_pos == $param2[0])
    					{
    						$getObjects[] = $v;
    					}
    				}
    			}
    		}
    		elseif(strcmp($param1,"SELECT COUNT(*) FROM `{$prefix}module_blocks` WHERE `s_pos`=? AND (`s_locale`=? OR `s_locale`='_ALL') AND (`s_query_hash`=? OR `s_query_hash`='_ALL') AND for_roles LIKE ? AND (`module`<>? OR `action`<>?)") == 0)
    		{
    			$sql = <<<SQL
SELECT COUNT(*) FROM `{$prefix}module_blocks` WHERE `s_pos`='$param2[0]' AND (`s_locale`='$param2[1]' OR `s_locale`='_ALL') AND (`s_query_hash`='$param2[2]' OR `s_query_hash`='_ALL') AND for_roles LIKE '$param2[3]' AND (`module`<>'$param2[4]' OR `action`<>'$param2[5]') 
SQL;
				$getObjects = Memorycache::FetchMemory($sql,"{$prefix}module_blocks","count",null);
    		}
    		else
    		{
    			$getObjects = "notmatch";
    		}
    		
    		if(empty($getObjects))
    		{
    			return "empty";
    		}
    		else
    		{
    			return $getObjects;
    		}
    	}
    }

	public static function getMyTemplate() {
        $o_mb = new ModuleBlock();
        $modules =& $o_mb->find("module=? and action=? and show_title='1' and s_pos='footer' and s_locale=? and s_query_hash='_ALL'", array('mod_static','custom_html',DEFAULT_LOCALE));
        $s_param= unserialize($modules->s_param);
		if(!strpos($s_param['html'],'http://www.sitestar.cn')){
			$db = MysqlConnection::get();
			$sql1 = "INSERT INTO ".Config::$tbl_prefix."module_blocks (`module`, `action`, `alias`, `title`, `show_title`, `s_pos`, `s_param`, `s_locale`, `s_query_hash`, `i_order`, `published`, `for_roles`, `s_token`, `perpage_show`) VALUES('mod_static','custom_html','mb_foot',NULL,'1','footer','a:1:{s:4:\"html\";s:320:\"Power by <a href=\'http://www.sitestar.cn/\' target=\'_blank\' title=\'建站之星(sitestar)网站建设系统\' style=\'display:inline;\'>建站之星</a>|<a href=\'http://www.cndns.com/\' target=\'_blank\' title=\'域名注册|域名申请|域名尽在“美橙互联”\' style=\'display:inline;\'>美橙互联</a>&nbsp;版权所有\";}','".DEFAULT_LOCALE."','_ALL',0,'1','{member}{admin}{guest}',NULL,NULL)";
			$rs =& $db->query($sql1);
		}
    }
}
?>