<?php
/**
 * ------------------------------------------------------------------------
 * JA News Pro Module for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 


if (!class_exists('JANewsHelperPro')) {
    
    require_once JPATH_SITE . DS . 'components' . DS . 'com_content' . DS . 'helpers' . DS . 'route.php';
    jimport('joomla.application.categories');
    JModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');
    
    /**
     * News Pro Module JA Article Helper
     *
     * @package		Joomla
     * @subpackage	Content
     * @since 		1.6
     */
    class JANewsHelperPro
    {


        /**
         * 
         * Get data Article 
         * @param object $helper object from JAHelperPro
         * @param object $params
         * @return object $helper object include data of Article
         */
        function getDatas(&$helper, $params)
        {
            
            $getChildren = $params->get('getChildren', 1);
            
            $catsid = $params->get('catsid');
            if (!is_array($catsid)) {
                $arr_cats[] = $catsid;
            } else {
                $arr_cats = $catsid;
            }
            $moduleid = $helper->moduleid;
            
            //$model = JModel::getInstance('Categories', 'ContentModel');
            // Can't show category when set Maximum Subcategories = 0
			if ($helper->get('maxSubCats', -1) == 0) {
				$arr_cats = array(-1);
			}

            foreach ($arr_cats as $catid) {
                $params_cat = new JParameter('');
                $cooki_name = 'mod' . $moduleid . '_' . $catid;
                if (isset($_COOKIE[$cooki_name]) && $_COOKIE[$cooki_name] != '') {
                    $cookie_user_setting = $_COOKIE[$cooki_name];
                    $arr_values = explode('&', $cookie_user_setting);
                    if ($arr_values) {
                        foreach ($arr_values as $row) {
                            list($k, $value) = explode('=', $row);
                            if ($k != '') {
                                $params_cat->set($k, $value);
                            }
                        }
                    }
                }
                
                $_categories = array();
                $_section = array();
                $articles = array();
                $cats = array();
                if (!$catid)
                    continue;
                
                $categories = JCategories::getInstance('Content');
                $_section = $categories->get($catid);
                
                //$_categories = $_section->getChildren();
                

                if ($_section && is_object($_section) && method_exists($_section, 'getChildren')) {
                    $_categories = $_section->getChildren();
                }
                
                if (!count($_section) && !count($_categories))
                    continue;
                $_categories_org = $_categories;
                
                $cookie_catsid = array();
                if ($params_cat->get('cookie_catsid', '') != '') {
                    $cookie_catsid = explode(',', $params_cat->get('cookie_catsid', ''));
                    if ($_categories) {
                        $temp = array();
                        foreach ($_categories as $k => $cat) {
                            if (in_array($cat->id, $cookie_catsid)) {
                                $temp[] = $_categories[$k];
                            }
                        }
                        $_categories = $temp;
                    }
                }
                
                $cat_link = JRoute::_(ContentHelperRoute::getCategoryRoute($_section->id));
                $cat_title = $_section->title;
                $cat_desc = $_section->description;
                
                if (count($_section) && count($_categories)) {
                    foreach ($_categories as $k => $cat) {
                        $_categories[$k]->link = JRoute::_(ContentHelperRoute::getCategoryRoute($cat->id));
                    }
                }
                
                if ($helper->get('groupbysubcat', 0)) {
                    
                    $maxSubCats = $params_cat->get('maxSubCats', $helper->get('maxSubCats', -1));
                    if ($maxSubCats == -1)
                        $maxSubCats = count($_categories);
                    
                    $temp = array();
                    if ($_categories) {
                        $i = 0;
                        foreach ($_categories as $k => $cat) {
                            $catids = array();
                            $subcats = array();
                            $catids[] = $cat->id;
                            if ($getChildren) {
                                $subcats = $cat->getChildren(true);
                                if ($subcats) {
                                    foreach ($subcats as $subcat) {
                                        $catids[] = $subcat->id;
                                    }
                                }
                            
                            }
                            
                            $rows = $this->getArticles($catids, $helper, $params_cat);
                            if ($rows) {
                                $temp[] = $cat;
                                $articles[$cat->id] = $rows;
                                $i++;
                                if ($i == $maxSubCats)
                                    break;
                            }
                        }
                        $_categories = $temp;
                    }
                } else {
                    $catids = array();
                    $catids[] = $catid;
                    
                    if ($getChildren && count($_categories)) {
                        $_all_Children = $_section->getChildren(true);
                        foreach ($_all_Children as $cat) {
                            $catids[] = $cat->id;
                        }
                    }
                    
                    $articles = $this->getArticles($catids, $helper, $params_cat);
                }
                
                $helper->articles[$catid] = $articles;
                $helper->_section[$catid] = $_section;
                $helper->_categories[$catid] = $_categories;
                $helper->_categories_org[$catid] = $_categories_org;
                $helper->cat_link[$catid] = $cat_link;
                $helper->cat_title[$catid] = $cat_title;
                $helper->cat_desc[$catid] = $cat_desc;
            }
        }


        /**
         * 
         * Get Articles
         * @param array $catids categories id
         * @param object $helper
         * @param object $params
         * @return object Article
         */
        function getArticles($catids, $helper, $params)
        {
            
            $mainframe = JFactory::getApplication();
            // Get the dbo
            $db = JFactory::getDbo();
            
            // Get an instance of the generic articles model
            $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));
            
            // Set application parameters in model
            $appParams = JFactory::getApplication()->getParams();
            $model->setState('params', $appParams);
            
            $model->setState('filter.published', 1);
            
            // Access filter
            $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
            $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
            $model->setState('filter.access', $access);
            
            if ($catids) {
                if ($catids[0] != "") {
                    $model->setState('filter.category_id', $catids);
                }
            }
            
            if ($helper->get("featured", "hide")) {
                $model->setState('filter.featured', $helper->get("featured", "hide"));
            }
            
            $sort_order = $helper->get('sort_order', 'DESC');
			
			switch ($helper->get('ordering', 'created')) {
				case 'ordering':
					$model->setState('list.ordering', 'a.ordering');
					$model->setState('list.direction', $sort_order);
					break;
					
				case 'rand':
					$model->setState('list.ordering', 'RAND()');
					break;
					
				case 'title': 
					$model->setState('list.ordering', 'a.title');
					$model->setState('list.direction', $sort_order);
					break;
					
				case 'hits': 
					$model->setState('list.ordering', 'a.hits');
					$model->setState('list.direction', $sort_order);
					break;
					
				case 'created': 
					$model->setState('list.ordering', $helper->get('ordering', 'created'));
					$model->setState('list.direction', $sort_order);
					break;	
				
				case 'modified': 
					$model->setState('list.ordering', $helper->get('ordering', 'created'));
					$model->setState('list.direction', $sort_order);
					break;				
			}
            
            if ($helper->get('timerange') > 0) {
                $model->setState('filter.date_filtering', 'relative');
                $model->setState('filter.date_field', 'a.created');
                $model->setState('filter.relative_date', $helper->get('timerange'));
            }
            
            $limit = (int) $params->get('introitems', $helper->get('introitems')) 
                   + (int) $params->get('linkitems', $helper->get('linkitems'));
            
            $model->setState('list.start', (int) $helper->get('limitstart', 0));
            $model->setState('list.limit', (int) $helper->get('limit', $limit));
            
            $rows = $model->getItems();
            
            if (!$rows)
                return array();
            
            if ($helper->get('JPlugins', 1)) {
                JPluginHelper::importPlugin('content');
                $dispatcher = & JDispatcher::getInstance();
                $com_params = & $mainframe->getParams('com_content');
            }
            
            $autoresize = intval(trim($helper->get('autoresize', 0)));
            $width_img 	= (int)$helper->get('width', 100) < 0 ? 100 : $helper->get('width', 100);
            $height_img = (int)$helper->get('height', 100) < 0 ? 100 : $helper->get('height', 100);
            $img_w = intval(trim($width_img));
            $img_h = intval(trim($height_img));
            
            $img_align = $helper->get('align', 'left');
            $showimage = $params->get('showimage', $helper->get('showimage', 0));
            $maxchars = intval(trim($helper->get('maxchars', 200)));
            $hiddenClasses = trim($helper->get('hiddenClasses', ''));
            $showdate = $helper->get('showdate', 0);
            $enabletimestamp = $helper->get('timestamp', 0);
            $showcreator = $helper->get('showcreator', 0);
            
            foreach ($rows as $i => $row) {
                $row->cat_link = JRoute::_(ContentHelperRoute::getCategoryRoute($row->catid));
                $row->text = $row->introtext;
                if ($helper->get('JPlugins', 1)) {
                    $dispatcher->trigger('onPrepareContent', array(& $row, & $com_params, 0));
                }
                if ($showcreator) {
                    $row->creator = JFactory::getUser($row->created_by);
                }
                $row->slug = $row->id . ':' . $row->alias;
                $row->catslug = $row->catid . ':' . $row->category_alias;
                $row->introtext = $row->text;
                if ($access || in_array($row->access, $authorised)) {
                    // We know that user has the privilege to view the article
                    $row->link = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
                } else {
                    $row->link = JRoute::_('index.php?option=com_user&view=login');
                }
                $row->introtext1 = $row->introtext;
                $row->image = $helper->replaceImage($row, $img_align, $autoresize, $maxchars, $showimage, $img_w, $img_h, $hiddenClasses);
                if ($maxchars == 0)
                    $row->introtext1 = '';
                
                if ($enabletimestamp)
                    $row->created = $helper->generatTimeStamp($row->modified);
                else
                    $row->created = JHTML::_('date', $row->modified);
                
                $rows[$i] = $row;
            }
            
            return $rows;
        }


        /**
         * 
         * Get total hits of Article item
         * @return int
         */
        function getTotalHits()
        {
            $db = & JFactory::getDBO();
            
            $query = 'SELECT MAX(hits)' . ' FROM #__content';
            $db->setQuery($query);
            return $db->loadResult();
        }
    }
}
?>