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

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Restricted access');
/**
 * Radio List Element
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldThemes extends JFormField
{
    /*
     * @var name element
     */
    protected $type = 'themes';


    /**
     * Fetch Ja Element Theme Param method
     * 
     * @return	object  param
     */
    function getInput()
    {
        $uri = str_replace(DS, "/", str_replace(JPATH_SITE, JURI::base(), dirname(__FILE__)));
        $uri = str_replace("/administrator/", "", $uri);
        if (!defined('japaramhelper2')) {
            JHTML::stylesheet($uri . "/assets/css/" . 'japaramhelper2.css');
            JHTML::script($uri . "/assets/js/" . 'japaramhelper2.js');
            
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            
            define('japaramhelper2', true);
        }
        
        /* Get all themes name folder from folder themes */
        $themes = array();
        
        // get in module
        $path = JPATH_SITE . DS . 'modules' . DS . 'mod_janewspro' . DS . 'tmpl';
        if (!JFolder::exists($path))
            return JText::_('Themes Folder not exist');
        $folders = JFolder::folders($path);
        if ($folders) {
            foreach ($folders as $fname) {
                $themes[$fname] = $fname;
            }
        }
        
        // get in template	 
        $template = JFormFieldJaparamhelper2::get_active_template();
        $path = JPATH_SITE . DS . 'templates' . DS . $template . DS . 'html' . DS . 'mod_janewspro';
        if (JFolder::exists($path)) {
            $folders = JFolder::folders($path);
            if ($folders) {
                foreach ($folders as $fname) {
                    $themes[$fname] = $fname;
                }
            }
        }
        
        $HTMLThemes = array();
        if ($themes) {
            foreach ($themes as $fname) {
                //
                $f = new stdClass();
                $f->id = $fname;
                $f->title = $fname;
                array_push($HTMLThemes, $f);
            }
        }
        
        $html = JHTML::_('select.genericlist', $HTMLThemes, @$matches[1][0] . '[' . $this->name . ']', 'style="width:150px; position:relative" onchange="japarams2.changeTheme(this.value, false)"', 'id', 'title', $this->value);
        return $html;
    }
}