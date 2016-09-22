<?php


if (!defined('IN_CONTEXT')) die('access violation error!');

/**
 * Constant NO_LAYOUT
 * Do not include layout while rendering module content
 */
if (!defined('NO_LAYOUT')) define('NO_LAYOUT', 620);

/**
 * Constant DFT_LAYOUT
 * Use default layout while rendering module content
 */
if (!defined('DFT_LAYOUT')) define('DFT_LAYOUT', 621);

/**
 * The module controller
 *
 * @package content
 */
class Module {
    /**
     * Filters apply to this module,
     * and with exception actions as its value.
     *
     * @access protected
     * @var array
     */
    protected $_filters = array();

    /**
     * Variable holder
     *
     * @access protected
     * @var array
     */
    protected $_view_vars = array();

    /**
     * The layout to use while rendering module content
     *
     * @access protected
     * @var int|string
     */
    protected $_layout = DFT_LAYOUT;

    /**
     * Apply filters
     *
     * @access protected
     * @param string $action The requested action
     */
    protected function _applyFilters($action) {
        if (sizeof($this->_filters) > 0) {
            foreach ($this->_filters as $filter_name => $exceptions) {
                if (strpos($exceptions, '{'.$action.'}') === false) {
                    if (file_exists(P_FLT.'/'.$filter_name.'.php')) {
                        include_once(P_FLT.'/'.$filter_name.'.php');
                    } else {
//						die("module2=".P_FLT.'/'.$filter_name.'.php');
                        Content::redirect(PAGE_404);
                    }
                    $filter_name_part = explode('_', $filter_name);
                    $filter_class_name = '';
                    foreach ($filter_name_part as $name_part) {
                        $filter_class_name .= ucfirst($name_part);
                    }
                    $filter_class = new $filter_class_name();
                    if (!$filter_class->execute()) {
                        return false;
                    }
                }
            }
        }
        
        if(!Toolkit::md5Filter()) {
        	return false;
        }
        
        return true;
    }

    /**
     * Run action
     *
     * @access public
     * @param string $_action The requested action
     * @param bool $_use_layout Whether to use the module layout file or not while rendering
     */
    public function execute($_action, $_use_layout = true) {
        /* filters before action */
        if (!$this->_applyFilters($_action) && R_TPE == '_page') {
            exit(1);
        }

        $_view = $this->$_action();
        /* filters after action */
        // hold the place

        $o_site =& SessionHolder::get('_SITE', false);
        if ($o_site) {
        	$this->setVar('_SITE', $o_site);
    	}

        if (sizeof($this->_view_vars) > 0) {
            foreach ($this->_view_vars as $var => $value) {
                $$var = $value;
            }
        }

        $_module_class_name = get_class($this);
        $_flat_module_class_name =
            Toolkit::transformClassName($_module_class_name);
		$_flat_module = strtolower(ParamHolder::get('_m'));

        //----------------前台调用共用view[start] hufh--------------------
		if(strpos($_SERVER['PHP_SELF'],'/admin/') === false)//前台模板机制
		{
	        if ($_view) {
	            $_view_file = P_TPL_VIEW.'/view/'.$_flat_module_class_name.'/'.$_view.'.php';
	        } else {
	            $_view_file = P_TPL_VIEW.'/view/'.$_flat_module_class_name.'/'.$_action.'.php';
	        }
		}
		else //后台
		{
			if ($_view) {
	            $_view_file = P_TPL.'/view/'.$_flat_module_class_name.'/'.$_view.'.php';
	        } else {
	            $_view_file = P_TPL.'/view/'.$_flat_module_class_name.'/'.$_action.'.php';
	        }
		}
		//----------------前台调用共用view[end] hufh----------------------
		
        if (!file_exists($_view_file)) {
            Content::redirect(PAGE_404);
        }
        if (!$_use_layout) {
            $this->_layout = NO_LAYOUT;
        }

        if(check_mod($_flat_module_class_name)) {
            if ($this->_layout == NO_LAYOUT) {
                include($_view_file);
            } else {
                //if ($this->_layout == DFT_LAYOUT) {
                if (preg_match('/^admin_/', $_action)) {
                    if (file_exists(P_TPL.'/layout/admin_'.$_flat_module_class_name.'.php')) {
                        $_layout_file = P_TPL.'/layout/admin_'.$_flat_module_class_name.'.php';
                    } else {
                        $_layout_file = P_TPL.'/layout/'.$this->_layout.'.php';
                    }
                } else {
                //-----------------hfh--------------------------
                	  //前台情况
					  if(file_exists(P_TPL.'/layout/layout.php')) 
					  {
					  	  if(!(strpos('mod_user|mod_offline|mod_tool|mod_navigation|mod_media|mod_email', R_MOD) ===  false) && !in_array(R_ACT, array('reg_form', 'edit_profile')))
					  	  {
					  	  		$_layout_file = ROOT."/view/layout/only_content.php";
					  	  }
					  	  else
					  	  {
					  	  	  //--------------支持每个页面不同布局样式[start]------------------
					  	  	  if(!empty($_GET))
					  	  	  {
					  	  	  	if(empty($_GET['_v']))
					  	  	  	{
									$url='';
									foreach($_GET as $k => $v)//根据navbar的url转跳地址寻找页面布局样式（偏左侧或偏右侧布局）
								  	{
								  		$url .= $k.'='.$v.'&';
								  	}
								  	$url = substr($url,0,strlen($url)-1);
					  	  	  	}
					  	  	  	else
					  	  	  	{
					  	  	  		$url = '_m=frontpage&_a=index';
					  	  	  	}
					  	  	  }
					  	  	  else
					  	  	  {
					  	  	  		$url = '_m=frontpage&_a=index';
					  	  	  }
					  	  	  
						  	  $menu_item = new MenuItem();
						  	  
						  	  $result = $menu_item->find("link='$url' AND s_locale = '".trim(SessionHolder::get('_LOCALE'))."'");
						  	  
						  	  include_once(ROOT.'/template/'.DEFAULT_TPL.'/layout/conf.php');
						  	  $layout_param = LayouConfig::$layout_param;
						  	  
						  	  if(empty($result) || $result->layout == 'default' || empty($result->layout))
						  	  {
						  	  		//如果url链接的页面找不到具体布局，则加载默认布局
						  	  		$_layout_file = P_TPL."/layout/{$layout_param['default']['layout_php_file']}";
						  	  }
						  	  else
						  	  {
						  	  		//获取新加页面的css布局
						  	  		$_layout_file = P_TPL."/layout/{$layout_param[$result->layout]['layout_php_file']}";
						  	  }
						  	  //--------------支持每个页面不同布局样式[end]--------------------
					  	  }
					  }
                	  elseif (file_exists(P_TPL.'/layout/'.$_flat_module_class_name.'.php')) 
                	  {
                        	$_layout_file = P_TPL.'/layout/'.$_flat_module_class_name.'.php';
                      } 
                      else
                      {
                       	 	$_layout_file = P_TPL.'/layout/'.$this->_layout.'.php';
                      }
			 	//-----------------hufh--------------------------
                }
                if (!file_exists($_layout_file)) {
                    Content::redirect(PAGE_404);
                }
                $_content_ = $_view_file;
                include_once($_layout_file);
				if(!strpos($_layout_file,"admin") && !strpos($_layout_file,"only_content")){
				if(Toolkit::getcorp()){
				if($GLOBALS['varfooter']!='1'){
				unset($GLOBALS['varfooter']);
			//$domain = $_SERVER['HTTP_HOST'];
			//$checkfooter = @file_get_contents('"http://'.$domain.'/"');
			//if(!strpos($checkfooter,'www.sitestar.cn')){
				die('<div>Power by
<a style="display:inline;" title="建站之星(sitestar)网站建设系统" target="_blank" href="http://www.sitestar.cn/">建站之星</a>
|
<a style="display:inline;" title="域名注册|域名申请|域名尽在“美橙互联”" target="_blank" href="http://www.cndns.com/">美橙互联</a>
 版权所有');
			//}
				}
				}
				}
            }
        } else {
            die('Module not supported!');
        }
    }

    /**
     * Add variable to holder
     *
     * @access protected
     * @param string $key The key name that will be accessible as variable in views
     * @param mixed $value The any type of variable associated with the key name
     */
    protected function setVar($key, $value) {
        $this->_view_vars[$key] = $value;
    }

    /**
     * Alias to setVar
     *
     * @access protected
     * @param string $key The key name that will be accessible as variable in views
     * @param mixed $value The any type of variable associated with the key name
     */
    protected function assign($key, $value) {
        $this->setVar($key, $value);
    }
}
?>
