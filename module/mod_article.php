<?php
if (!defined('IN_CONTEXT')) die('access violation error!');

class ModArticle extends Module {
    protected $_filters = array(
        'check_login' => '{recentarticles}{article_content}{fullist}{recentshort}'
    );
    
    private $stack = array();
    private $findout = array();

    public function fullist() {
        $this->_layout = 'frontpage';

        // The default article category
        $curr_article_category = new ArticleCategory();

        $caa_id = trim(ParamHolder::get('caa_id', '0'));
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
 		$page_title = new MenuItem();         
        $title_info = $page_title->find(" `link`=? and  s_locale=?",array("_m=mod_article&_a=fullist",$curr_locale)," limit 1"); 
		if(isset($title_info->name)){
			$curr_article_category->name = $title_info->name;
		}else{
			$curr_article_category->name = '';
		}
        $search_where = '';
        $search_params = array();
        $article_keyword = trim(ParamHolder::get('article_keyword', '',PS_POST))?Toolkit::baseEncode(trim(ParamHolder::get('article_keyword', '',PS_POST))):trim(ParamHolder::get('article_keyword', '',PS_GET)); 
        $article_keyword = Toolkit::baseDecode($article_keyword);
        if (strlen($article_keyword) > 0) {
            $search_where = ' AND (title LIKE ? OR content LIKE ?)';
            $search_params = array('%'.$article_keyword.'%', '%'.$article_keyword.'%');
            $this->assign('article_keyword', $article_keyword);
        }// 02/06/2010 Edit >>
        else if (intval($caa_id) > 1) {
        //if (intval($cap_id) > 1) {
        // 02/06/2010 Edit <<
        	$article_category = new ArticleCategory();
        	$article_categories = $article_category->findAll();
        	if(empty($article_categories)) $article_categories = array();
        	foreach($article_categories as $k => $v)
        	{
        		$this->stack[$v->id] = $v->article_category_id;
        	}
        	$this->findout[] = $caa_id;
        	$this->getCategoryList();
        	$search_where = " AND article_category_id IN ('' ";
    		foreach($this->findout as $k => $v)
    		{
    			$search_where .= ",$v";
    		}
//    		$len = strlen($search_where);
//    		$search_where[$len-1] = ''; 
    		$search_where .= ') AND article_category_id <> 0';//die($search_where);
            $curr_article_category = new ArticleCategory($caa_id);
        }
        try {
            $now = time();
            $o_article = new Article();
            /**
             * Add 02/08/2010
             */
            include_once(P_LIB.'/pager.php');

            if (ACL::requireRoles(array('admin'))) {
            	$str_sql = "((`pub_start_time`<? AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`<? AND `pub_end_time`='-1') OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`='-1')) AND "
                            ."published='1' AND article_category_id<>2";
//                if(empty($caa_id))
//                {
//                	$str_sql .= " AND article_category_id=2";//article_category is news.
//                }         
                
                $article_data =&
                    Pager::pageByObject('article',
                        $str_sql. " AND s_locale=?".$search_where,
                        array_merge(array($now, $now, $now, $now, $curr_locale), $search_params),
                        "ORDER BY `i_order` DESC,`create_time` DESC");
            } else {
            	$str_sql = "((`pub_start_time`<? AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`<? AND `pub_end_time`='-1') OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`='-1')) AND "
                            ."published='1' AND article_category_id<>2";
//            	if(empty($caa_id))
//                {
//                	$str_sql .= " AND article_category_id=2";//article_category is news.
//                }         
                         
                $article_data =&
                    Pager::pageByObject('article',
                        $str_sql .= " AND for_roles LIKE ? AND s_locale=?".$search_where,
                        array_merge(array($now, $now, $now, $now, '%'.$user_role.'%', $curr_locale), $search_params),
                        "ORDER BY `i_order` DESC,`create_time` DESC");
            }
			if(isset($curr_article_category->name)){
				$this->assign('page_title', $curr_article_category->name);
			}else{
				$this->assign('page_title', '');
			}

            $this->assign('category', $curr_article_category);
            $this->assign('articles', $article_data['data']);
            $this->assign('pager', $article_data['pager']);
            $this->assign('page_mod', $article_data['mod']);
			$this->assign('page_act', $article_data['act']);
			$this->assign('page_extUrl', $article_data['extUrl']);
            $this->assign('caa_id', $caa_id);
            $this->assign('article_keyword', $article_keyword);
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_error';
        }

    }

    public function article_content() {
        $this->_layout = 'frontpage';
        $article_id = ParamHolder::get('article_id', '0');
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        if (intval($article_id) == 0) {
            ParamParser::goto404();
        }
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        try {
            $now = time();
            $o_article = new Article();
            if (ACL::requireRoles(array('admin'))) {
                $curr_article =& $o_article->find("`id`=? AND "
                            ."((`pub_start_time`<? AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`<? AND `pub_end_time`='-1') OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`='-1')) AND "
                            ."published='1' AND article_category_id<>2",
                        array($article_id, $now, $now, $now, $now));
				if($curr_article){
					$curr_article->v_num++;
					$curr_article->save();
				}else{
					ParamParser::goto404();
				}
            } else {
                $curr_article =& $o_article->find("`id`=? AND "
                            ."((`pub_start_time`<? AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`<? AND `pub_end_time`='-1') OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`>=?) OR "
                            ."(`pub_start_time`='-1' AND `pub_end_time`='-1')) AND "
                            ."published='1' AND article_category_id<>2 AND for_roles LIKE ?",
                        array($article_id, $now, $now, $now, $now, '%'.$user_role.'%'));
							//var_dump(sizeof($curr_article));
                if(sizeof($curr_article) > 0) {
					if($curr_article){
						$curr_article->v_num++;
						$curr_article->save();
					}else{
					 	ParamParser::goto404();
					}
                } else {
                	ParamParser::goto404();
                }
            }
            include_once(P_LIB.'/pager.php');
            $page_title = new MenuItem();       	 
        	
            if ($page_title->count(" `link`=?  and  s_locale=?",array("_m=mod_article&_a=article_content&article_id={$article_id}",$curr_locale))) {
         		$title_info = $page_title->find(" `link`=?  and  s_locale=?",array("_m=mod_article&_a=article_content&article_id={$article_id}",$curr_locale)," limit 1 "); 
         		$this->assign('page_title', $title_info->name);
            }else{
            	$this->assign('page_title', $curr_article->title);
            }
            $article_category_id = $curr_article->article_category_id;
            $nextAndPrevArr = $this->getNextAndPrev($article_id,$article_category_id);
            $content=$curr_article->content;
            $article_data=&Pager::pageByText( $content,array('article_id'=>$article_id));
            $curr_article->content=$article_data['data'];
            $this->assign('page_mod', $article_data['mod']);
		    $this->assign('page_act', $article_data['act']);
		  	$this->assign('page_extUrl', $article_data['extUrl']);
            $this->assign('pagetotal', $article_data['total']);
            $this->assign('pagenum', $article_data['cur_page']);
            $this->assign('curr_article', $curr_article);
            $this->assign('nextAndPrevArr', $nextAndPrevArr);
        } catch (Exception $ex) {
            $this->assign('json', Toolkit::jsonERR($ex->getMessage()));
            return '_error';
        }
    }

    public function recentarticles() {
        $list_size = trim(ParamHolder::get('article_reclst_size'));
        if (!is_numeric($list_size) || strlen($list_size) == 0) {
            $list_size = '5';
        }
        $article_category = ParamHolder::get('article_category_list', '0');

		if(empty($article_category)){
			 $article_category='0';
		}

        $this->assign('article_category', $article_category);
        $curr_locale = trim(SessionHolder::get('_LOCALE'));
        // 02/06/2010 Add >>
        $childids = $this->getCategoryChildIds($article_category, $curr_locale);
        $article_category = !empty($childids) ? $childids.$article_category : $article_category;
        $article_category = $this->arrUnique($article_category);
        // 02/06/2010 Add <<
        $o_article = new Article();
        $user_role = trim(SessionHolder::get('user/s_role', '{guest}'));
        if (ACL::requireRoles(array('admin'))) {
            if($article_category == 0) {
                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? ", array($curr_locale),
                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);
            } else {
//                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? and article_category_id=? ", array($curr_locale,$article_category),
//                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);		
                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? and article_category_id IN(".$article_category.") ", array($curr_locale),
                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);            }
        } else {
			if($article_category == 0) {
                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? AND for_roles LIKE ? ", array($curr_locale, '%'.$user_role.'%'),
                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);
            } else {
//                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? and article_category_id=? AND for_roles LIKE ? ", array($curr_locale,$article_category, '%'.$user_role.'%'),
//                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);
                $articles = $o_article->findAll("article_category_id<>2 AND published='1' AND s_locale=? and article_category_id IN(".$article_category.") AND for_roles LIKE ? ", array($curr_locale, '%'.$user_role.'%'),
                            "ORDER BY `i_order` DESC, `create_time` DESC LIMIT ".$list_size);
            }
        }
        $this->assign('article_category', $article_category);
        $this->assign('articles', $articles);
    }

    public function recentshort() {
        return $this->recentarticles();
    }
    
	public function getCategoryList()
    {
    	$i = $j = count($this->stack);
    	$flag = true;
    	while(($j < $i) || $flag)
    	{
	    	$i = $j;
    		foreach($this->stack as $k => $v)
	    	{
	    		if(in_array($v,$this->findout))
	    		{
	    			$this->findout[] = $k;
	    			unset($this->stack[$k]);
	    		}
	    	}
	    	$j = count($this->stack);
	    	$flag = false;
    	}
    }
    
    // 02/06/2010 Add >>
    private function getCategoryChildIds( $parentid, $curr_locale )
    {
//			echo $parentid;
//    	$article_childcategories = array();
//    	$article_category = new ArticleCategory();
//    	$article_childcategories = $article_category->findAll("article_category_id IN({$parentid}) AND s_locale = '{$curr_locale}'");
//    	
//    	$childids = '';
//    	if ( count($article_childcategories) ) {
//    		foreach( $article_childcategories as $val )
//    		{
//    			$childids .= $val->id.',';
//				$childids .= $this->getCategoryChildIds($val->id, $curr_locale);
//    		}
//    	}
//    	
//    	return $childids;
		$where="s_locale = '{$curr_locale}' ";		
		$childids = array();		
		$par_ids=explode(',', $parentid);	
		foreach($par_ids as $parent_id){
			$procategories=ArticleCategory::listCategories($parent_id, $where);		
			$childarr=$this->fetchIdstr($procategories);
			$childids=array_merge($childids,$childarr);
		}
		$childstr=implode(',', $childids);
		if(!empty($childstr)) $childstr.=',';
		
		return $childstr;
    }
		
	private function fetchIdstr($catearr){
		$ids=array();
		foreach($catearr as $cate){
			$ids[]=$cate->id;
			if(!empty($cate->slaves['ArticleCategory'])){
					$childids=$this->fetchIdstr($cate->slaves['ArticleCategory']);
					$ids=array_merge($ids,$childids);
			}
		}
		
		return $ids;
	}
    
    private function arrUnique($str) {
    	$arrtmp = $result = array();
    	if (empty($str) || !isset($str)) {
    		return '0';
    	} else if (strrpos($str, ",") === false) {
			return $str;
    	} else {
    		$arrtmp = explode(",", $str);
    		$result = array_unique($arrtmp);
    		return join(",", $result);
    	}
    }
    
    private function getNextAndPrev($id,$article_category_id){
    	$curr_locale = trim(SessionHolder::get('_LOCALE'));
    	$prev = array();
    	$next = array();
    	$arr = array();
		$arr2 = array();
		$o_article = new Article();
        $articles =& $o_article->findAll(" published='1' AND article_category_id=".$article_category_id,array());
		
		foreach($articles as $article){
			$arr[$article->id] = $article->title;
		} 
		ksort($arr);
		$count = count($arr);
		$j = 0;
		foreach($arr as $k=>$v){
			$arr2[$j]['id'] = $k;
			$arr2[$j]['title'] = $v;
			$j++;
		}		
		for ($i = 0; $i < $count; $i++){
			if($arr2[$i][id]==$id){
				if($count==1){
					$prev['str'] = __("No prev product")."<br>";
					$next['str'] = __("No next product")."<br>";
				}else{
					if($i==0){
						$prev['str'] = __("No prev article")."<br>";
						$next['id'] = $arr2[$i+1]['id'];
						$next['title'] = $arr2[$i+1]['title'];
					}elseif($i==$count-1){
						$next['str'] = __("No next article")."<br>";
						$prev['id'] = $arr2[$i-1]['id'];
						$prev['title'] = $arr2[$i-1]['title'];
					}else{
						$prev['id'] = $arr2[$i-1]['id'];
						$prev['title'] = $arr2[$i-1]['title'];
						$next['id'] = $arr2[$i+1]['id'];
						$next['title'] = $arr2[$i+1]['title'];
					}
				}
			}
		}
        $str = '<div><font style="color:#595959">'.__('Prev article').'</font>：';
        $str .= is_string($prev['str'])?$prev['str']:"<a href=".Html::uriquery('mod_article', 'article_content', array('article_id' =>$prev['id'])).">".$prev['title']."</a><br>";
        $str .= '<font style="color:#595959">'.__('Next article').'</font>：';
        $str .= is_string($next['str'])?$next['str']:"<a href=".Html::uriquery('mod_article', 'article_content', array('article_id' => $next['id'])).">".$next['title']."</a></div>";
        return $str;
               
    }
}
?>
