<?php


if (!defined('IN_CONTEXT')) die('access violation error!');

class ModStatic extends Module {
    protected $_filters = array(
        'check_login' => '{view}{custom_html}{company_intro}{seo}'
    );
    
    public function view() {
        $this->_layout = 'frontpage';
    	include_once(P_LIB.'/pager.php');
        $sc_id = ParamHolder::get('sc_id', '0');
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        if (intval($sc_id) == 0) {
            ParamParser::goto404();
        }
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        try {
            $o_scontent = new StaticContent();
            //wl  11-03-04
        //check table static_contents 
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        $count_num = $o_scontent->count("s_locale=?",array($curr_locale),"ORDER BY `id` DESC");
        if ($count_num<"2") {
        	echo "<script>alert('".__("Page data has error,rebulid it please!")."');</script>";
        }//end
            $menu_items = new MenuItem();// for show title
            if (ACL::requireRoles(array('admin'))) {
                $curr_scontent =& $o_scontent->find("`id`=? AND s_locale=?", array($sc_id, $curr_locale));
                // for show title
//                $curr_menuitem =& $menu_items->find("`link`='_m=mod_static&_a=view&sc_id={$sc_id}' AND "
//                            ."published='1' AND s_locale=?", 
//                        array($curr_locale));
            } else {
                $curr_scontent =& $o_scontent->find("`id`=? AND for_roles LIKE ? AND s_locale=?", 
                        array($sc_id, '%'.$user_role.'%', $curr_locale));
               	// for show title
//               	$curr_menuitem =& $menu_items->find("`link`='_m=mod_static&_a=view&sc_id={$sc_id}' AND "
//                            ."published='1' AND for_roles LIKE ? AND s_locale=?", 
//                        array('%'.$user_role.'%', $curr_locale));
            }
//            $this->assign('cur_title', $curr_menuitem->name);
			//分页
			if (!$curr_scontent) {
				ParamParser::goto404();
				exit;
			}
 			$source_data = &Pager::pageByText( $curr_scontent->content,array('sc_id'=>$sc_id));
 			$curr_scontent->content=$source_data['data'];
            $this->assign('page_mod', $source_data['mod']);
		    $this->assign('page_act', $source_data['act']);
		  	$this->assign('page_extUrl', $source_data['extUrl']);
            $this->assign('pagetotal', $source_data['total']);
            $this->assign('pagenum', $source_data['cur_page']);
            //结束
			$page_cat = isset($curr_scontent->title)?$curr_scontent->title:'';
            $this->assign('page_cat', $page_cat);
            $this->assign('curr_scontent', $curr_scontent);
        } catch (Exception $ex) {
            ParamParser::goto404();
        }
    }
    
    public function custom_html() {
    	//echo ParamHolder::get('block_id', '');
        $this->setVar('html', ParamHolder::get('html', ''));
    }
	
    
    public function company_intro() {
    	$curr_locale = trim(SessionHolder::get('_LOCALE'));
    	$user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
    	
    	if (preg_match('/^\d+$/i', trim(ParamHolder::get('cpy_intro_number')))) {
    		$cpy_intro_number = trim(ParamHolder::get('cpy_intro_number'));
    	} else {
    		$cpy_intro_number = 150;
    	}
    	try {
    		// get company_intro id
    		$staticontent_info = array();
    		$ot_staticontent = new StaticContent();
    		 //wl  11-03-04
	        //check table static_contents 
	        $curr_locale = trim(SessionHolder::get('_LOCALE'));
	        $count_num = $ot_staticontent->count("s_locale=?",array($curr_locale),"ORDER BY `id` DESC");
	        if ($count_num<"2") {
	        	echo "<script>alert('".__("Page data has error,rebulid it please!")."');</script>";
	        }//end
	        $staticontent_data = $ot_staticontent->findAll("s_locale=? AND published='1'", array($curr_locale), "ORDER BY `id`  LIMIT 2");
	        $company_id = $staticontent_data[1]->id;
	        
            $o_scontent = new StaticContent();
    	    if (ACL::requireRoles(array('admin'))) {
                $curr_scontent =& $o_scontent->find("`id`=? AND "
                            ."published='1' AND s_locale=?", 
                        array($company_id, $curr_locale));
            } else {
                $curr_scontent =& $o_scontent->find("`id`=? AND "
                            ."published='1' AND for_roles LIKE ? AND s_locale=?", 
                        array($company_id, '%'.$user_role.'%', $curr_locale));
            }
            $this->assign('curr_scontent', $curr_scontent);
            $this->assign('cpy_intro_number', $cpy_intro_number);
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_error';
        }
    }
}
?>