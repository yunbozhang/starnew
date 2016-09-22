<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

class ModMessage extends Module {
    protected $_filters = array(
        'check_login' => '{form}{messInsert}'
    );
    
	public function form() {
    	$this->_layout = 'layout';

		$curr_locale = trim(SessionHolder::get('_LOCALE'));
        $message = new MenuItem();         
        $message_info = $message->find(" `mi_category`=? and s_locale=? ",array("message",$curr_locale)," order by id limit 1");
        $page_cat = $message_info->name;   
        $this->assign('page_cat', $page_cat);
    	return 'form';
    }
    
	public function messInsert() {
		$mess_info =& ParamHolder::get('mess', array());
		try {
			// 昵称
			if (!preg_match('/^(?!_|\s\')[A-Za-z0-9_\x80-\xff\s\']+$/', $mess_info['username'])) {
				$this->assign('json', Toolkit::jsonERR(__('Invalid nickname!')));
				return '_result';
			} 
			// 电子邮件
			else if (!preg_match('/^[ _a-z0-9- ]+(\.[a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/',$mess_info['email']))
			{
				$this->assign('json', Toolkit::jsonERR(__('Invalid email address!')));
				return '_result';
			}
			// 电话
			else if (!preg_match('/^[0-9\-]+$/', $mess_info['tele']) && SITE_LOGIN_VCODE) {
				$this->assign('json', Toolkit::jsonERR(__('Invalid telephone number!')));
				return '_result';
			}
			// 验证码
			else if (isset($mess_info['rand_rs'])&&!RandMath::checkResult($mess_info['rand_rs'])) {
	            $this->setVar('json', Toolkit::jsonERR(__('Sorry! Please have another try with the math!')));
	            return '_result';
			}else {
				$o_mess = new Message();
				$mess_info['create_time'] = time();
				$o_mess->set($mess_info);
				$o_mess->save();
				$this->assign('json', Toolkit::jsonOK(array('forward' => Html::uriquery('mod_message', 'form'))));
		 		return '_result';
			}
		} catch (Exception $ex) {
			$this->assign('json', Toolkit::jsonERR($ex->getMessage()));
			return '_result';
		}
		
	}
}
?>
