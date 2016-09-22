<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

class ModBulletin extends Module {
    protected $_filters = array(
        'check_login' => '{recentbulletins}{bulletin_content}'
    );

    public function recentbulletins() {
        /*$list_size = trim(ParamHolder::get('bulletin_reclst_size'));
        if (!is_numeric($list_size) || strlen($list_size) == 0) {
            $list_size = '5';
        }*/
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        $bulletin_type = trim(ParamHolder::get('bulletin_type', '0'));
        
        $o_bulletin = new Bulletin();
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        if (ACL::requireRoles(array('admin'))) {
        	$bulletins = $o_bulletin->findAll("published='1' AND s_locale=? ", array($curr_locale),
                            "ORDER BY `create_time` DESC");
        } else {
            $bulletins = $o_bulletin->findAll("published='1' AND s_locale=? AND for_roles LIKE ? ", array($curr_locale,'%'.$user_role.'%'),
                        "ORDER BY `create_time` DESC");
        }

        $this->assign('bulletins_list', $bulletins);
        $this->assign('bulletin_type', $bulletin_type);
        $this->assign("randstr", ToolKit::randomStr());
    }

	public function bulletin_content() {
		$this->_layout = 'frontpage';
        $bulletin_id = ParamHolder::get('bulletin_id', '0');
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        if (intval($bulletin_id) == 0) {
            ParamParser::goto404();
        }
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        try {
        	$o_bulletin = new Bulletin();
	        if (ACL::requireRoles(array('admin'))) {
	        	$curr_bulletin = $o_bulletin->findAll("published='1' AND id='{$bulletin_id}' AND s_locale=? ", array($curr_locale),
	                            "ORDER BY `create_time` DESC");
	        } else {
	            $curr_bulletin = $o_bulletin->findAll("published='1' AND id='{$bulletin_id}' AND s_locale=? AND for_roles LIKE ? ", array($curr_locale,'%'.$user_role.'%'),
	                        "ORDER BY `create_time` DESC");
	        }
	        
	        $this->assign('curr_bulletin', $curr_bulletin[0]);
             $this->assign('page_title', isset($curr_bulletin[0])?$curr_bulletin[0]->title:'');
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_error';
        }
	}
}
?>
