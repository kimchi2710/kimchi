<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

/**
 * ------------------------------------------------------------------------
 * JA News Featured module for Joomla 1.7
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
 */

if (!class_exists('JAK2HelperFP')) {
    /**
     * News Front Page Module JA K2 Helper
     *
     * @package		Joomla
     * @subpackage	Content
     * @since 		1.5
     */
    class JAK2HelperFP
    {


        /**
         *
         * Get Articles of K2
         * @param object $helper
         * @param object $params
         * @return object
         */
        function getArticles(&$helper, $params)
        {
            if (! file_exists(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php')) {
				return ;
			}
            require_once (JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');

            $helper->set('getChildren', $params->get('getChildren', 1));
            $catsid = $params->get('k2catsid');
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }

            JArrayHelper::toInteger($catids);
            if ($catids) {
                if ($catids && count($catids) > 0) {
                    foreach ($catids as $k => $catid) {
                        if (!$catid)
                            unset($catids[$k]);
                    }
                }
            }

            jimport('joomla.filesystem.file');
            $limit = (int) $helper->get('limit', 10);
            if (!$limit)
                $limit = 4;
            $limitstart = (int) $helper->get('limitstart', 0);
            $ordering = $helper->get('ordering', '');
            $sort_order = $helper->get('sort_order','DESC');
            $componentParams = &JComponentHelper::getParams('com_k2');

            $user = &JFactory::getUser();
            $aid = $user->get('aid') ? $user->get('aid') : 1;
            $db = &JFactory::getDBO();

            $jnow = &JFactory::getDate();
            $now = $jnow->toMySQL();
            $nullDate = $db->getNullDate();

			$query 	= "SELECT i.*, c.name AS categoryname,c.id AS categoryid, c.alias AS categoryalias, c.name as cattitle, c.params AS categoryparams";
            $query .= "\n FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";
            $query .= "\n WHERE i.published = 1 AND i.featured=1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
            $query .= "\n AND ( i.publish_up = " . $db->Quote($nullDate) . " OR i.publish_up <= " . $db->Quote($now) . " )";
            $query .= "\n AND ( i.publish_down = " . $db->Quote($nullDate) . " OR i.publish_down >= " . $db->Quote($now) . " )";
			
			switch ($params->get('featured')) {
				case 'hide': 
					$query = str_replace("AND i.featured=1", "AND i.featured=0", $query);
					break;
					
				case 'show': 
					$query = str_replace("AND i.featured=1", " ", $query);
					break;
			}
			
            if ($catids) {
                $catids_new = $catids;
                if ($helper->get('getChildren')) {
                    foreach ($catids as $k => $catid) {
                        $subcatids = JAK2HelperFP::getK2CategoryChildren($catid, true);
                        if ($subcatids) {
                            $catids_new = array_merge($catids_new, array_diff($subcatids, $catids_new));
                        }
                    }
                }
                $catids = implode(',', $catids_new);
                $query .= "\n AND i.catid IN ($catids)";
            }

            switch ($ordering) {
                case 'ordering':
                    $ordering = 'featured_ordering';
                    break;

                case 'rorder':
                    $ordering = 'featured_ordering DESC';
                    break;

                case 'rand':
                    $ordering = 'RAND()';
                    break;
            }
			
            if ($ordering == 'RAND()')
                $query .= "\n ORDER BY " . $ordering.' '.$sort_order;
            else
                $query .= "\n ORDER BY i." . $ordering .' '.$sort_order.", i.id desc";
            $db->setQuery($query, $limitstart, $limit);
            $rows = $db->loadObjectList();

            $autoresize = intval(trim($helper->get('autoresize', 1)));

            $img_align = trim($helper->get('align', 'left'));
            $hiddenClasses = trim($helper->get('hiddenClasses', ''));

            $bigmaxchar = $helper->get('bigmaxchars', 200);
            $bigimg_w = (int)$helper->get('bigimg_w', 150) < 0 ? 150 : $helper->get('bigimg_w', 150);
            $bigimg_h = (int)$helper->get('bigimg_h', 100) < 0 ? 100 : $helper->get('bigimg_h', 100);
            $bigshowimage = $helper->get('bigshowimage', 1);

            $smallmaxchar = $helper->get('smallmaxchars', 100);
           	$smallimg_w = (int)$helper->get('smallimg_w', 80) < 0 ? 80 : $helper->get('smallimg_w', 80) ; 
            $smallimg_h = (int)$helper->get('smallimg_h', 80) < 0 ? 80 : $helper->get('smallimg_h', 80) ;
            $smallshowimage = $helper->get('smallshowimage', 1);

            if (count($rows)) {

                foreach ($rows as $j => $row) {

                    //Clean title
                    $row->title = JFilterOutput::ampReplace($row->title);

                    $row->bigintrotext = $row->introtext;
                    $row->smallintrotext = $row->introtext;

                    // Introtext
                    $row->text = $row->introtext;

                    //Read more link
                    $row->link = urldecode(JRoute::_(K2HelperRoute::getItemRoute($row->id . ':' . urlencode($row->alias), $row->catid . ':' . urlencode($row->categoryalias))));

                    $helper->_params->set('parsedInModule', 1);

                    $dispatcher = &JDispatcher::getInstance();

                    if ($helper->get('JPlugins', 1)) {

                        //Plugins
                        $results = $dispatcher->trigger('onBeforeDisplay', array(&$row, &$helper->_params, $limitstart));
                        $row->event->BeforeDisplay = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onAfterDisplay', array(&$row, &$helper->_params, $limitstart));
                        $row->event->AfterDisplay = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onAfterDisplayTitle', array(&$row, &$helper->_params, $limitstart));
                        $row->event->AfterDisplayTitle = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onBeforeDisplayContent', array(&$row, &$helper->_params, $limitstart));
                        $row->event->BeforeDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onAfterDisplayContent', array(&$row, &$helper->_params, $limitstart));
                        $row->event->AfterDisplayContent = trim(implode("\n", $results));

                        $dispatcher->trigger('onPrepareContent', array(&$row, &$helper->_params, $limitstart));
                        $row->introtext = $row->text;

                    }

                    //Init K2 plugin events
                    $row->event->K2BeforeDisplay = '';
                    $row->event->K2AfterDisplay = '';
                    $row->event->K2AfterDisplayTitle = '';
                    $row->event->K2BeforeDisplayContent = '';
                    $row->event->K2AfterDisplayContent = '';
                    $row->event->K2CommentsCounter = '';

                    //K2 plugins
                    if ($helper->get('K2Plugins', 1)) {
                        JPluginHelper::importPlugin('k2');

                        $results = $dispatcher->trigger('onK2BeforeDisplay', array(&$row, &$helper->_params, $limitstart));
                        $row->event->K2BeforeDisplay = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onK2AfterDisplay', array(&$row, &$helper->_params, $limitstart));
                        $row->event->K2AfterDisplay = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onK2AfterDisplayTitle', array(&$row, &$helper->_params, $limitstart));
                        $row->event->K2AfterDisplayTitle = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onK2BeforeDisplayContent', array(&$row, &$helper->_params, $limitstart));
                        $row->event->K2BeforeDisplayContent = trim(implode("\n", $results));

                        $results = $dispatcher->trigger('onK2AfterDisplayContent', array(&$row, &$helper->_params, $limitstart));
                        $row->event->K2AfterDisplayContent = trim(implode("\n", $results));

                        $dispatcher->trigger('onK2PrepareContent', array(&$row, &$helper->_params, $limitstart));
                        $row->introtext = $row->text;
                    }

                    //Clean the plugin tags
                    $row->introtext = preg_replace("#{(.*?)}(.*?){/(.*?)}#s", '', $row->introtext);

                    //Images
                    $image = '';
                    if (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_XL.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_XL.jpg';

                    elseif (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_XS.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_XS.jpg';

                    elseif (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_L.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_L.jpg';

                    elseif (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_S.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_S.jpg';

                    elseif (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_M.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_M.jpg';

                    elseif (JFile::exists(JPATH_SITE . DS . 'media' . DS . 'k2' . DS . 'items' . DS . 'cache' . DS . md5("Image" . $row->id) . '_Generic.jpg'))
                        $image = JURI::root() . 'media/k2/items/cache/' . md5("Image" . $row->id) . '_Generic.jpg';

                    if ($image != '') {
                        $thumbnailMode = $helper->get('thumbnail_mode', 'crop');
                        $aspect = $helper->get('use_ratio', '1');
                        $crop = $thumbnailMode == 'crop' ? true : false;
                        $align = $img_align ? "align=\"$img_align\"" : "";

                        $jaimage = JAImage::getInstance();
                        if ($thumbnailMode != 'none' && $jaimage->sourceExited($image)) {
                            $smallimageURL = $jaimage->resize($image, $smallimg_w, $smallimg_h, $crop, $aspect);
                            $row->smallimage = $smallimageURL ? "<img src=\"" . $smallimageURL . "\" alt=\"{$row->title}\" $align />" : "";

                            $bigimageURL = $jaimage->resize($image, $bigimg_w, $bigimg_h, $crop, $aspect);
                            $row->bigimage = $bigimageURL ? "<img src=\"" . $bigimageURL . "\" alt=\"{$row->title}\" $align />" : "";
                        } else {
                            $width = $bigimg_w ? "width=\"$bigimg_w\"" : "";
                            $height = $bigimg_h ? "height=\"$bigimg_h\"" : "";
                            $row->bigimage = "<img class=\"$img_align\" src=\"" . $image . "\" alt=\"{$row->title}\" $width $height $align />";

                            $width = $smallimg_w ? "width=\"$smallimg_w\"" : "";
                            $height = $smallimg_h ? "height=\"$smallimg_h\"" : "";
                            $row->smallimage = "<img class=\"$img_align\" src=\"" . $image . "\" alt=\"{$row->title}\" $width $height $align />";
                        }

                        if ($bigmaxchar && strlen($row->introtext) > $bigmaxchar) {
                            $doc = JDocument::getInstance();
                            if (function_exists('mb_substr')) {
                                $row->bigintrotext = SmartTrim::mb_trim($row->introtext, 0, $bigmaxchar, $doc->_charset);
                            } else {
                                $row->bigintrotext = SmartTrim::trim($row->introtext, 0, $bigmaxchar);
                            }
                        } elseif ($bigmaxchar == 0) {
                            $row->bigintrotext = '';
                        }

                        if ($smallmaxchar && strlen($row->introtext) > $smallmaxchar) {
                            $doc = JDocument::getInstance();
                            if (function_exists('mb_substr')) {
                                $row->smallintrotext = SmartTrim::mb_trim($row->introtext, 0, $smallmaxchar, $doc->_charset);
                            } else {
                                $row->smallintrotext = SmartTrim::trim($row->introtext, 0, $smallmaxchar);
                            }
                        } elseif ($smallmaxchar == 0) {
                            $row->smallintrotext = '';
                        }
                    } else {
                        $introtext = $row->introtext;
                        $row->bigimage = $helper->replaceImage($row, $img_align, $autoresize, $bigmaxchar, $bigshowimage, $bigimg_w, $bigimg_h, $hiddenClasses);
                        $row->bigintrotext = $row->introtext1;
                        if ($bigmaxchar == 0)
                            $row->bigintrotext = '';
                        $row->introtext = $introtext;
                        $row->smallimage = $helper->replaceImage($row, $img_align, $autoresize, $smallmaxchar, $smallshowimage, $smallimg_w, $smallimg_h, $hiddenClasses);
                        $row->smallintrotext = $row->introtext1;
                        if ($smallmaxchar == 0)
                            $row->smallintrotext = '';
                    }
                    $row->introtext = '<p>' . $row->introtext . '</p>';
                    $row->bigintrotext = '<p>' . $row->bigintrotext . '</p>';
                    $row->smallintrotext = '<p>' . $row->smallintrotext . '</p>';

                    //Author
                    if ($helper->get('showcreator')) {
                        if (!empty($row->created_by_alias)) {
                            $row->creator = $row->created_by_alias;
                            $row->authorGender = NULL;
                        } else {
                            $author = &JFactory::getUser($row->created_by);
                            $row->creator = $author->name;
                            $query = "SELECT `gender` FROM #__k2_users WHERE userID=" . (int) $author->id;
                            $db->setQuery($query, 0, 1);
                            $row->authorGender = $db->loadResult();
                            //Author Link
                            $row->authorLink = JRoute::_(K2HelperRoute::getUserRoute($row->created_by));
                        }
                    }

                    $rows[$j] = $row;
                }
            }

            return $rows;
        }


        /**
         *
         * Get K2 category children
         * @param int $catid
         * @param boolean $clear if true return array which is removed value construction
         * @return array
         */
        function getK2CategoryChildren($catid, $clear = false) {

    		static $array = array();
    		if ($clear)
    		$array = array();
    		$user = &JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
    		$catid = (int) $catid;
    		$db = &JFactory::getDBO();
    		$query = "SELECT * FROM #__k2_categories WHERE parent={$catid} AND published=1 AND trash=0 AND access<={$aid} ORDER BY ordering ";
    		$db->setQuery($query);
    		$rows = $db->loadObjectList();

    		foreach ($rows as $row) {
    			array_push($array, $row->id);
    			if (JAK2HelperFP::hasK2Children($row->id)) {
    				JAK2HelperFP::getK2CategoryChildren($row->id);
    			}
    		}
    		return $array;
    	}


    	/**
    	 *
    	 * Check category has children
    	 * @param int $id
    	 * @return boolean
    	 */
    	function hasK2Children($id) {

    		$user = &JFactory::getUser();
    		$aid = $user->get('aid') ? $user->get('aid') : 1;
    		$id = (int) $id;
    		$db = &JFactory::getDBO();
    		$query = "SELECT * FROM #__k2_categories WHERE parent={$id} AND published=1 AND trash=0 AND access<={$aid} ";
    		$db->setQuery($query);
    		$rows = $db->loadObjectList();

    		if (count($rows)) {
    			return true;
    		} else {
    			return false;
    		}
    	}


        /**
         *
         * Get total K2 items
         * @param object $helper
         * @param object $params
         * @return int
         */
        function getTotal(&$helper, $params)
        {
            if (! file_exists(JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php')) {
				return ;
			}
            require_once (JPATH_SITE . DS . 'components' . DS . 'com_k2' . DS . 'helpers' . DS . 'route.php');

            $helper->set('getChildren', $params->get('getChildren', 1));
            $catsid = $params->get('k2catsid');
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }

            JArrayHelper::toInteger($catids);
            if ($catids) {
                if ($catids && count($catids) > 0) {
                    foreach ($catids as $k => $catid) {
                        if (!$catid)
                            unset($catids[$k]);
                    }
                }
            }

            $user = &JFactory::getUser();
            $aid = $user->get('aid');
            $db = &JFactory::getDBO();

            $jnow = &JFactory::getDate();
            $now = $jnow->toMySQL();
            $nullDate = $db->getNullDate();

			$query 	= "SELECT count(i.id) ";
            $query .= "\n FROM #__k2_items as i LEFT JOIN #__k2_categories c ON c.id = i.catid";
            $query .= "\n WHERE i.published = 1 AND i.featured=1 AND i.access <= {$aid} AND i.trash = 0 AND c.published = 1 AND c.access <= {$aid} AND c.trash = 0";
            $query .= "\n AND ( i.publish_up = " . $db->Quote($nullDate) . " OR i.publish_up <= " . $db->Quote($now) . " )";
            $query .= "\n AND ( i.publish_down = " . $db->Quote($nullDate) . " OR i.publish_down >= " . $db->Quote($now) . " )";
			
			switch ($params->get('featured')) {
				case 'hide': 
					$query = str_replace("AND i.featured=1", "AND i.featured=0", $query);
					break;
					
				case 'show': 
					$query = str_replace("AND i.featured=1", " ", $query);
					break;
			}
			
			if ($catids) {
                $catids_new = $catids;
                if ($helper->get('getChildren')) {
                    foreach ($catids as $k => $catid) {
                        if (!$catid)
                            continue;
                        $subcatids = JAK2HelperFP::getK2CategoryChildren($catid, true);
                        if ($subcatids) {
                            $catids_new = array_merge($catids_new, array_diff($subcatids, $catids_new));
                        }
                    }
                }
                $catids = implode(',', $catids_new);
                $query .= "\n AND i.catid IN ($catids)";
            }

            $db->setQuery($query);
            return $db->loadResult();
        }
    }
}

?>