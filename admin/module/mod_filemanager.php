<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

class ModFilemanager extends Module{
	protected $_filters = array(
        'check_admin' => ''
    );
    
    public function admin_list() {
    	//$this->_layout = 'content';
    	$this->_layout = 'multiload';
    }
    
    public function admin_dashboard() {
    	$this->_layout = 'default';
    }
    
    public function admin_detail() {
    	$this->_layout = 'default';
    }
    
    public function file_rename() {
    	// 文件重命名
    	$err = '0';
    	$param = trim(ParamHolder::get('_p',''));
    	$newname = trim(ParamHolder::get('_f',''));
    	if (!empty($param) && !empty($newname)) {
    		$param = urldecode($param);
    		$basepath = substr($param, strpos($param, '|')+1, strrpos($param, '/')-strpos($param, '|'));
			$oldname = substr($param, strpos($param, '|')+1);
			
			// 中文名转码
			if (preg_match("/^WIN/i", PHP_OS)) {
				if (preg_match("/[\x80-\xff]./", $oldname)) $oldname = iconv('UTF-8', 'GBK//IGNORE', $oldname);
		   		if (preg_match("/[\x80-\xff]./", $newname)) $newname = iconv('UTF-8', 'GBK//IGNORE', $newname);
		    }
		    // 重命名开始
		    if (!file_exists($oldname)) {
		    	$err = '-2';
		    } elseif (file_exists($basepath.$newname) && ($oldname != $basepath.$newname)) {
		    	$err = '-3';
		    } else {
		    	if (@rename($oldname, $basepath.$newname) === false) $err = '-4';
		    }
    	} else {
    		$err = '-1';
    	}
    	
		if ($err != '0') {
			$this->setVar('json', Toolkit::jsonERR($err));
		} else {
			$this->setVar('json', Toolkit::jsonOK());
		}
		
		return '_result';
    }
    
    public function file_delete() {
    	$err = '0';
    	$param = trim(ParamHolder::get('_p',''));
    	
    	if (!empty($param)) {
    		$param = urldecode($param);
    		$delfile = substr($param, strpos($param, '|')+1);
    		// 中文名转码
			if (preg_match("/^WIN/i", PHP_OS) && preg_match("/[\x80-\xff]./", $delfile)) {
				$delfile = iconv('UTF-8', 'GBK//IGNORE', $delfile);
		    }
		    // 删除文件开始
		    if (!file_exists($delfile)) {
		    	$err = '-2';
		    } else {
		    	Toolkit::removeDir($delfile);
		    }
    	} else {
    		$err = '-1';
    	}
    	
		if ($err != '0') {
			$this->setVar('json', Toolkit::jsonERR($err));
		} else {
			$this->setVar('json', Toolkit::jsonOK());
		}
		
		return '_result';
    }
    
    public function make_dir() {
    	$err = '0';
    	$basedir = trim(ParamHolder::get('basedir',''));
    	$newdir = trim(ParamHolder::get('newdir',''));
    	// is or not exist dir
    	$hd = dir(ROOT."/$basedir");
    	while(($path = $hd->read()) !== false) {
    		if ($path == $newdir) {
    			$err = '-1';
    			break;
    		} else continue;
    	}
    	
    	if ($err != '-1') {
    		if (!mkdir("../{$basedir}{$newdir}", 0755)) $err = '-2';
    	}
    	
    	if ($err == '0') {
    		$this->setVar('json', Toolkit::jsonOK());
    	} else {
    		$this->setVar('json', Toolkit::jsonERR($err));
    	}
    	
    	return '_result';
    }
}
?>