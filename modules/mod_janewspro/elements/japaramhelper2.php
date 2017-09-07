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
jimport('joomla.html.parameter');
jimport('joomla.form.form');
require_once ("japaramhelper.php");
/**
 * Radio List Element
 *
 * @since      Class available since Release 1.2.0
 */
class JFormFieldJaparamhelper2 extends JFormFieldJaparamhelper
{
    /*
	 * @var string param type
	 */
    protected $type = 'Japaramhelper2';


    /**
     *
     * Get Profile of Module
     * @return Ambigous <string, multitype:>|string
     */
    protected function getProfile()
    {
        if (!defined('_JA_PARAM_HELPER')) {
            define('_JA_PARAM_HELPER', 1);
            $uri = str_replace(DS, "/", str_replace(JPATH_SITE, JURI::base(), dirname(__FILE__)));
			if( strpos($uri,JURI::base()) != 0 && strpos($uri,JURI::base()) == false )
			{
			 $uri = JURI::base().$uri;
			}
            $uri = str_replace("/administrator/", "", $uri);
            
			//mootools support joomla 1.7 and 2.5
			JHTML::_('behavior.framework', true);
			
            JHTML::stylesheet($uri . '/assets/css/japaramhelper.css');
            JHTML::script($uri . '/assets/js/japaramhelper.js');
        }
        if (!defined('JAPARAMERTER2')) {
            $uri = str_replace(DS, "/", str_replace(JPATH_SITE, JURI::base(), dirname(__FILE__)));
			if( strpos($uri,JURI::base()) != 0 && strpos($uri,JURI::base()) == false )
			{
			 $uri = JURI::base().$uri;
			}
            $uri = str_replace("/administrator/", "", $uri);

            JHTML::stylesheet($uri . "/assets/css/japaramhelper2.css");
            JHTML::script($uri . "/assets/js/japaramhelper2.js");

            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');

            define('JAPARAMERTER2', true);
        }

        $jsonData = array();
        $folder_profiles = array();

        /* Get all profiles name folder from folder profiles */
        $profiles = array();
        $jsonData = array();
        $jsonTempData = array();

        // get in template
        $template = $this->get_active_template();

        $path = JPATH_SITE . DS . 'templates' . DS . $template . DS . 'html' . DS . 'mod_janewspro';
        if (JFolder::exists($path)) {
            $files = JFolder::files($path, '.ini');
            if ($files) {
                foreach ($files as $fname) {
                    $fname = substr($fname, 0, -4);

                    $f = new stdClass();
                    $f->id = $fname;
                    $f->title = $fname;

                    $profiles[$fname] = $f;

                    $params = new JParameter(JFile::read($path . DS . $fname . '.ini'));
                    $jsonData[$fname] = $params->toArray();
                    $jsonTempData[$fname] = $jsonData[$fname];
                }
            }
        }
        // get in module
        $path = JPATH_SITE . DS . 'modules' . DS . 'mod_janewspro' . DS . 'profiles';
        if (!JFolder::exists($path))
            return JText::_('PROFILE_FOLDER_NOT_EXIST');
        $files = JFolder::files($path, '.ini');
        if ($files) {
            foreach ($files as $fname) {
                $fname = substr($fname, 0, -4);

                $f = new stdClass();
                $f->id = $fname;
                $f->title = $fname;

                $profiles[$fname] = $f;

                $params = new JParameter(JFile::read($path . DS . $fname . '.ini'));
                $jsonData[$fname] = $params->toArray();
            }
        }

        $xml_profile = JPATH_SITE . DS . 'modules' . DS . 'mod_janewspro' . DS . 'admin' . DS . 'config.xml';
        if (file_exists($xml_profile)) {
            /* For General Form */
            $options = array("control" => "jaform");
            $paramsForm = &JForm::getInstance('jform', $xml_profile, $options);
        }

        $HTML_Profile = JHTML::_('select.genericlist', $profiles, '' . $this->name, 'style="width:150px;" onchange="japarams2.changeProfile(this.value)"', 'id', 'title', $this->value);

        $_body = JResponse::getBody();
        ob_start();
        require_once dirname(__FILE__) . DS . 'tpl.php';
        $content = ob_get_clean();

        JResponse::setBody($_body);
        return $content;
    }
    /**
     * render 	function = categories
     * return list categories
     */
    protected function categories()
    {
    	$extension 	= $this->element['extension'];
    	$config 	= array('filter.published' => array(0, 1));
    	$hash 		= md5($extension.'.'.serialize($config));
    
    
    	$config	= (array) $config;
    	$db		= JFactory::getDbo();
    	$query	= $db->getQuery(true);
    
    	$query->select('a.id, a.title, a.level, a.parent_id');
    	$query->from('#__categories AS a');
    	$query->where('a.parent_id > 0');
    
    	// Filter on extension.
    	$query->where('extension = '.$db->quote($extension));
    
    	// Filter on the published state
    	if (isset($config['filter.published'])) {
    		if (is_numeric($config['filter.published'])) {
    			$query->where('a.published = '.(int) $config['filter.published']);
    		} elseif (is_array($config['filter.published'])) {
    			JArrayHelper::toInteger($config['filter.published']);
    			$query->where('a.published IN ('.implode(',', $config['filter.published']).')');
    		}
    	}
    
    	$query->order('a.lft');
    
    	$db->setQuery($query);
    	$items = $db->loadObjectList();
    
    	$control_name = 'jform';
    	$name = $this->element['name'];
    	$flag = false;
    	//Return label
		$HTMLSelect = '<label title="'.JTEXT::_('SELECT_CATEGORIES').'<br />'.JTEXT::_('SELECT_CATEGORIES_DESC').'" class="hasTip" for="' . $control_name . '_params_' . $name . '" id="' . $control_name . '_params_' . $name . '-lbl">'.JTEXT::_('SELECT_CATEGORIES').'</label>';
    	//Return select box
    	$HTMLSelect .= '<select name="' . $control_name . '[params][' . $name . '][]" id="' . $control_name . '_params_' . $name . '" class="inputbox" multiple="multiple" size="10">';
    
    	$HTMLCats = '';
    	$value = $this->value;
    	foreach ($items as $item) {
    		if(isset($item->id) && $item->id > 0){
    			$repeat = ( $item->level - 1 >= 0 ) ? $item->level - 1 : 0;
    			$item->title = str_repeat('- ', $repeat).$item->title;
    			$check = '';
    			if ((is_array($value) && in_array($item->id, $value)) || (!is_array($value) && $item->id == $value)) {
    				$flag = true;
    				$check = 'selected="selected"';
    			}
    
    			$class = '';
    			if ($repeat)
    				$class = 'class="subcat"';
    
    			$HTMLCats .= '<option value="' . $item->id . '" ' . $check . ' ' . $class . '>' . '&nbsp;&nbsp;&nbsp;' . $item->title . '</option>';
    		}
    	}
    
    	$HTMLSelect .= $HTMLCats;
    	$HTMLSelect .= '</select>';
    	return $HTMLSelect;
    }

    function getProfileParms()
    {

    }

    /**
     * Get tamplate actived current
     * @return string template name
     */
    function get_active_template(){
		$db =& JFactory::getDBO();

		// Get the current default template
		$query = ' SELECT template '
				.' FROM #__template_styles '
				.' WHERE client_id = 0'
				.' AND home = 1 ';
		$db->setQuery($query);
		$template = $db->loadResult();

		return $template;
	}


    /**
     * render js to control setting form.
     * @return	string  group param
     */
    function group()
    {
        preg_match_all('/jform\\[([^\]]*)\\]/', $this->name, $matches);
        $group_name = "jform";
        if ($this->checkArrEmpty($matches)) {
            preg_match_all('/jaform\\[([^\]]*)\\]/', $this->name, $matches);
            $group_name = "jaform";
        }
        ?>
<script type="text/javascript">
			<?php
        foreach ($this->element->children() as $option) {
            ?>
				<?php
            $str_els = trim((string) $option);
            ?>
				<?php
            $str_els = str_replace("\n", '', $str_els)?>
				<?php
            $hideRow = isset($option['hideRow']) ? '' . $option['hideRow'] . '' : 1;
            ?>
				japh_addgroup ('<?php
            echo $option['for'];
            ?>', { val: '<?php
            echo $option['value'];
            ?>', els_str: '<?php
            echo $str_els?>', group:'<?php
            echo $group_name;
            ?>[<?php
            echo @$matches[1][0]?>]', hideRow: <?php
            echo $hideRow?>});
			<?php
        }
        ;
        ?>
		</script>
<?php
        return;
    }

    /**
     *
     * Check array is empty
     * @param array $arr
     * @return boolean
     */
    function checkArrEmpty($arr = array())
    {
        $check = false;
        if (!empty($arr)) {
            $count = count($arr);
            $i = 0;
            foreach ($arr as $key => $item) {
                if (empty($item)) {
                    $i++;
                }
            }
            if ($i == $count)
                $check = true;
        }
        return $check;
    }
}

