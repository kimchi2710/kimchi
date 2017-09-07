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

if (!class_exists('JANewsHelperFP')) {
    /**
     * News Featured Module JA Article Helper
     *
     * @package		Joomla
     * @subpackage	Content
     * @since 		1.6
     */
    class JANewsHelperFP
    {


        /**
         *
         * Get data Article
         * @param object $helper object from JAHelperFP
         * @param object $params
         * @return object $helper object include data of Article
         */
        function getArticles(&$helper, $params)
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

            $catsid = $params->get('catsid');
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }
            // Category filter
            if ($catids) {
                if ($catids[0] != "") {
                    $model->setState('filter.category_id', $catids);
                }
            }

            if ($params->get("featured", "hide")) {
                $model->setState('filter.featured', $params->get("featured", "hide"));
            }

            if ($params->get('ordering', 'created') == "ordering") {
                if ($params->get("featured", "hide") == "only") {
                    $model->setState("list.ordering", "fp.ordering");
                } else {
                    $model->setState('list.ordering', 'a.ordering');
                }
                $model->setState('list.direction', $params->get('sort_order', 'DESC'));
            } else if ($params->get('ordering', 'created') == "rand") {
                $model->setState('list.ordering', 'RAND()');
            } else {
                $model->setState('list.ordering', $params->get('ordering', 'created'));
                $model->setState('list.direction', $params->get('sort_order', 'DESC'));
            }

            $model->setState('list.start', (int) $helper->get('limitstart', 0));
            $model->setState('list.limit', (int) $helper->get('limit', 10));

            $rows = $model->getItems();

            if ($helper->get('JPlugins', 1)) {
                JPluginHelper::importPlugin('content');
                $dispatcher = & JDispatcher::getInstance();
                $com_params = & $mainframe->getParams('com_content');
            }

            $autoresize = intval(trim($helper->get('autoresize', 1)));

            $img_align = trim($helper->get('align', 'left'));
            $hiddenClasses = trim($helper->get('hiddenClasses', ''));

            $bigmaxchar = $helper->get('bigmaxchars', 200);
            $bigimg_w = (int)$helper->get('bigimg_w', 150) < 0 ? 150 : $helper->get('bigimg_w', 150);
            $bigimg_h = (int)$helper->get('bigimg_h', 100)< 0 ? 100 : $helper->get('bigimg_h', 100);
            $bigshowimage = $helper->get('bigshowimage', 1);

            $smallmaxchar = $helper->get('smallmaxchars', 100);
            $smallimg_w = $helper->get('smallimg_w', 80) == -1 ? 80 : $helper->get('smallimg_w', 80) ; 
            $smallimg_h = $helper->get('smallimg_h', 80) == -1 ? 80 : $helper->get('smallimg_h', 80) ;
            $smallshowimage = $helper->get('smallshowimage', 1);

            $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
            $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));

            for ($i = 0; $i < count($rows); $i++) {
                $rows[$i]->text = $rows[$i]->introtext;
                if ($helper->get('JPlugins', 1)) {
                    $dispatcher->trigger('onPrepareContent', array(& $rows[$i], & $com_params, 0));
                }
                $rows[$i]->slug = $rows[$i]->id . ':' . $rows[$i]->alias;
                $rows[$i]->catslug = $rows[$i]->catid . ':' . $rows[$i]->category_alias;
                $rows[$i]->introtext = $rows[$i]->text;

                if ($access || in_array($rows[$i]->access, $authorised)) {
                    // We know that user has the privilege to view the article
                    $rows[$i]->link = JRoute::_(ContentHelperRoute::getArticleRoute($rows[$i]->slug, $rows[$i]->catslug));
                } else {
                    $rows[$i]->link = JRoute::_('index.php?option=com_user&view=login');
                }
                //$rows [$i]->link   		= JRoute::_(ContentHelperRoute::getArticleRoute($rows [$i]->slug, $rows [$i]->catslug, $rows [$i]->sectionid));
                $rows[$i]->bigimage = $helper->replaceImage($rows[$i], $img_align, $autoresize, $bigmaxchar, $bigshowimage, $bigimg_w, $bigimg_h, $hiddenClasses);
                $rows[$i]->bigintrotext = $rows[$i]->introtext1;
                if ($bigmaxchar == 0)
                    $rows[$i]->bigintrotext = '';
                $rows[$i]->introtext = $rows[$i]->text;
                $rows[$i]->smallimage = $helper->replaceImage($rows[$i], $img_align, $autoresize, $smallmaxchar, $smallshowimage, $smallimg_w, $smallimg_h, $hiddenClasses);
                $rows[$i]->smallintrotext = $rows[$i]->introtext1;
                if ($smallmaxchar == 0)
                    $rows[$i]->smallintrotext = '';
            }

            return $rows;
        }


        /**
         *
         * Get Articles
         * @param array $catids categories id
         * @param object $helper
         * @param object $params
         * @return object Article
         */
        function getTotal(&$helper, $params)
        {

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

            $catsid = $params->get('catsid');
            $catids = array();
            if (!is_array($catsid)) {
                $catids[] = $catsid;
            } else {
                $catids = $catsid;
            }
            // Category filter
            if ($catids) {
                if ($catids[0] != "") {
                    $model->setState('filter.category_id', $catids);
                }
            }
            if ($params->get("featured", "hide")) {
                $model->setState('filter.featured', $params->get("featured", "hide"));
            }

            $items = $model->getItems();
            return count($items);

        }
    }
}
?>