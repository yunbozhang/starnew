<?php


if (!defined('IN_CONTEXT')) die('access violation error!');

class ModAuth extends Module {
    protected $_filters = array(
        'check_login' => '{loginform}{loginregform}{dologin}{dologout}{open_auth}{auth_callback}'
    );
    
    public function loginform() {
    	$this->_layout = 'frontpage';
    	$this->assign('page_title', __('Login'));
        if (SessionHolder::get('user/s_role', '{guest}') != '{guest}') {
            // Do simply action override
            $this->userinfo();	
            return 'userinfo';
        } else {
            $forward_url = ParamHolder::get('_f', '');
            if (strlen(trim($forward_url)) == 0) {
                $forward_url = 'index.php';
            }
		  $accounts=ThirdAccount::findAll2(" active=1 ");

		$theaccounts=array();
		foreach($accounts as $theacc){
			$theaccounts[$theacc['account_type']]=$theacc;
		}
		
		
		$this->assign('accounts',$theaccounts);
            $this->setVar('forward_url', $forward_url);
        }
    }
    
	public function open_auth(){
		$this->_layout = NO_LAYOUT;
		
		$type=$_REQUEST['type'];
		try{
			$className=$this->auth_lib($type);
			if(empty($className)) die('Failed!');
			$authclass=new $className();
			Content::redirect($authclass->getAuthorizeURL());
		}catch(OAuthException $e){
			die($e->errormsg);
		}catch(Exception $e){
			die('Failed!');
		}
		exit();
	}	
	
	public function auth_callback(){
		$this->_layout = NO_LAYOUT;
		$type=$_REQUEST['type'];
		$code=$_REQUEST['code'];
		try{
			$className=$this->auth_lib($type);
			if(empty($className)) die('Failed!');
			$authclass=new $className();
			$authclass->processCallback();
			$this->assign('return_type', $authclass->return_val);
		}catch(OAuthException $e){
			$this->assign('error_code', $e->error);
			$this->assign('error_message', $e->errormsg);
			$this->assign('return_type', 3);
		}catch(LocalAuthException $e){
			$this->assign('error_message', $e->getMessage());
			$this->assign('return_type', 4);
		}
	}
	
    public function loginregform() {
    	$this->_layout = 'frontpage';
    	$this->assign('page_title', __('Login'));
        $forward_url = ParamHolder::get('_f', '');
        /**
         * for bugfree 350 14:38 2010-7-23 Add start
         */
        $goto =& SessionHolder::get('goto');
        if ((MOD_REWRITE == 2) && !empty($goto)) {
        	$forward_url = $goto;
        	// destroy session
        	SessionHolder::set('goto', '');
        }
        /**
         * for bugfree 350 14:38 2010-7-23 Add end
         */
        if (strlen(trim($forward_url)) == 0) {
            $forward_url = 'index.php';
        }
        $this->setVar('forward_url', $forward_url);
    }
    
    public function userinfo() {
    	global $db;
    	$user_id = SessionHolder::get('user/id', '0');
    	$sql = "select * from ".Config::$tbl_prefix."emails where user_id=".$user_id." and is_read=0";
    	$res = $db->query($sql);
    	$rows = $res->fetchRows();
        $curr_user = new User($user_id);
        $this->setVar('curr_user', $curr_user);
        $this->assign("read",count($rows));
    }

    public function dologin() {
    	$captcha = ParamHolder::get('rand_rs') ? ParamHolder::get('rand_rs') : ParamHolder::get('rand_rs_reglogn');
        if (!RandMath::checkResult($captcha)) {
            $this->setVar('json', Toolkit::jsonERR(__('Sorry! Please have another try with the math!')));
            return '_result';
        }

        if (ACL::loginUser(ParamHolder::get('login_user', ''), 
            ParamHolder::get('login_pwd', ''),'client')) {
            // 26/04/2010 Add <<
//            if (SessionHolder::get('role', '{guest}') == '{admin}') {
            if (ACL::isRoleAdmin()) {
            	$this->setVar('json', Toolkit::jsonERR(__('Administrator prohibit login!')));
            } else if(MEMBER_VERIFY=='1' && SessionHolder::get('user/member_verify')!='1'){
				SessionHolder::destroy();
				$this->setVar('json', Toolkit::jsonERR(__('being reviewed')));
			}else if(SessionHolder::get('user/active')!='1'){
				SessionHolder::destroy();
				$this->setVar('json', Toolkit::jsonERR(__('This account was prohibited from login, please contact the administrator.')));
			}else{// 26/04/2010 Add <<
            	$forward_url = ParamHolder::get('_f', '');
	            if (strlen(trim($forward_url)) == 0) {
	                $forward_url = 'index.php';
	            }
	            $this->setVar('json', Toolkit::jsonOK(array('forward' => $forward_url)));
            }
            
        } else {
            $this->setVar('json', Toolkit::jsonERR(__('Username and password mismatch!')));
        }
        
        return '_result';
    }
    
    public function dologout() {
        SessionHolder::destroy();
        // TODO: We need a logged out page and countdown redirecting to index.php
        //Content::redirect(Html::uriquery('mod_auth', 'loginform'));
        Content::redirect('index.php');
    }
		
	private function auth_lib($type){
		return UserOauth::auth_lib($type);
	}	
}
?>