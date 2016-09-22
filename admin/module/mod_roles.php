<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

class ModRoles extends Module {
   protected $_filters = array(
        'check_admin' => ''
    );
    
	
    public function admin_list() {
      $this->_layout = 'content';
	 if (!Toolkit::isSiteStarAuthorized()) {
		  $this->setVar('json', Toolkit::jsonERR(__('No Licenses!')));
            return '_error';
        }
	$user_data =& Pager::pageByObject('Role','id>3',array(),'order by id asc');

        $this->assign('roles', $user_data['data']);
        $this->assign('pager', $user_data['pager']);
        $this->assign('page_mod', $user_data['mod']);
	$this->assign('page_act', $user_data['act']);
	$this->assign('page_extUrl', $user_data['extUrl']);
    }
    
    public function admin_add() {
       $this->_layout = 'content';

    }
    
   public function admin_edit() {
        $this->_layout = 'content';

        $curr_role_id = trim(ParamHolder::get('u_id', '0'));
        if (intval($curr_role_id) == 0) {
            $this->assign('json', Toolkit::jsonERR(__('Invalid ID!')));
            return '_error';
        }
        try {
            $curr_role= new Role($curr_role_id);
            $this->assign('curr_role', $curr_role);
	  $paramstr=$curr_role->permission;
	  if(empty($paramstr)) $param=array();
	  else $param=unserialize($paramstr);
	   $this->assign('permissions', $param);

        } catch (Exception $ex) {
            $this->assign('json', $ex->getMessage());
            return ('_error');
        }
    }
	
    public function admin_create() {

        $role_info =@ ParamHolder::get('role', array());
        if (sizeof($role_info) <= 0) {
            $this->assign('json', Toolkit::jsonERR(__('Missing user information!')));
            return '_result';
        }
        $permission=@ ParamHolder::get('permission', array());
        if(empty( $permission)) $paramstr='';
        else $paramstr=serialize($permission);
         $o_role= new Role();
//print_r($permission);
           

        try {
           $role_info['name']='admin_temp';
           $role_info['permission']='';
            $o_role->set($role_info);
            $o_role->save();
	  $o_role->name="admin_".$o_role->id;
	  $o_role->permission=$paramstr;
	  $o_role->save();

            // TODO: send registration mail
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_result';
        }

        $this->assign('json', Toolkit::jsonOK(array('forward' => Html::uriquery('mod_roles', 'admin_list'))));
        return '_result';
    }
	
   public function admin_update() {

        $role_info =@ ParamHolder::get('role', array());
        if (sizeof($role_info) <= 0) {
            $this->assign('json', Toolkit::jsonERR(__('Missing user information!')));
            return '_result';
        }
        $permission=@ ParamHolder::get('permission', array());
        if(empty( $permission)) $paramstr='';
        else $paramstr=serialize($permission);
         $o_role= new Role($role_info['id']);
//print_r($permission);
           

        try {
           $role_info['permission']=$paramstr;
            $o_role->set($role_info);
            $o_role->save();

            // TODO: send registration mail
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_result';
        }

        $this->assign('json', Toolkit::jsonOK(array('forward' => Html::uriquery('mod_roles', 'admin_list'))));
        return '_result';

    }
	
      public function admin_delete() {

        $curr_role_id = trim(ParamHolder::get('u_id', '0'));
        if (intval($curr_role_id) <= 3) {
            $this->assign('json', Toolkit::jsonERR(__('Invalid ID!')));
            return '_result';
        }
        try {
            $curr_role = new Role($curr_role_id);
	 $o_user=new User();
	  $rolename=$curr_role->name;
	  $userinfo=$o_user->find("s_role=? ", array("{".$rolename."}"));
            if ($userinfo) {
                $this->assign('json', Toolkit::jsonERR(__('Cannot delete roles which is using!')));
                return '_result';
            } else {
                $curr_role->delete();
               // $db = MysqlConnection::get();
            	//$db->query('DELETE FROM es_user_extends WHERE user_id = ?',array($curr_role_id));
            }
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return ('_result');
        }

        $this->assign('json', Toolkit::jsonOK());
        return '_result';
    }
    
}
?>
