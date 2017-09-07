<?php
/**
 * ------------------------------------------------------------------------
 * JA News Featured Module for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.parameter');
jimport('joomla.form.form');

if (!defined('_JA_NEWSFP_')) {
    define('_JA_NEWSFP_', 1);
    require_once (dirname(__FILE__) . DS . 'jaimage.php');
    require_once (JPATH_SITE . '/components/com_content/helpers/route.php');
    /**
     * mod JA New Featured Helper class.
     */
    class modJaNewsFrontpageHelper extends JObject
    {
        /**
         *
         * module object
         * @var object
         */
        var $_module = null;
        /**
         *
         * module params object
         * @var object
         */
        var $_params = null;
        /**
         *
         * article object
         * @var object
         */
        var $articles = array();
        /**
         *
         * total of content
         * @var int
         */
        var $total = 0;


        /**
         * Constructor
         *
         * For php4 compatability we must not use the __constructor as a constructor for plugins
         * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
         * This causes problems with cross-referencing necessary for the observer design pattern.
         *
         * @param	object	$module The object to observe
         * @param	object 	$params	The object config of plugin
         */
        function __construct($module, $params = null)
        {
            $this->_module = $module;
            $this->_params = $this->loadProfile($params, $module->module);
        }


        /**
         *
         * Load profile of ja news featured
         * @param object $params parametters of ja news featured module
         * @param string $modulename name
         * @return object parametters of profile in janews featured
         */
        public static function loadProfile($params, $modulename = "mod_janews_featured")
        {
            $mainframe = JFactory::getApplication();
            $profilename = $params->get('profile', 'hallowen2');
            $params_new = new JParameter('');

            if (!empty($profilename)) {
                $path = JPATH_ROOT . DS . "modules" . DS . $modulename . DS . "admin" . DS . "config.xml";
                $ini_file = JPATH_ROOT . DS . "modules" . DS . $modulename . DS . "profiles" . DS . $profilename . ".ini";
                $config_content = "";
                if (file_exists($ini_file)) {
                    $config_content = JFile::read($ini_file);
                }

                if (empty($config_content)) {
                    $config_content = '';
                    $ini_file_in_temp = JPATH_SITE . DS . 'templates' . DS . $mainframe->getTemplate() . DS . 'html' . DS . $modulename . DS . $profilename . ".ini";
                    if (is_file($ini_file_in_temp)) {
                        $config_content = JFile::read($ini_file_in_temp);
                    }
                }

                if (file_exists($path)) {
                    $params_new = new JParameter($config_content);
                }
            }

            $params_new->set('limit', (int) $params_new->get('numberofheadlinenews'));
            //$params_new->set('groupbysubcat', $params->get('groupbysubcat', 0));
            $params_new->set('source', $params->get('source', 'JANewsHelper'));
            $params_new->set('ordering', $params->get('ordering', 'ordering'));
            $params_new->set('JPlugins', $params->get('JPlugins', 1));
            $params_new->set('K2Plugins', $params->get('K2Plugins', 1));

            return $params_new;
        }


        /**
         *
         * Load content with param type ( article, k2 )
         * @param object $params
         * @param int $moduleid
         * @return object content
         */
        function _load($params)
        {
            $mainframe = JFactory::getApplication();

            $source = $this->get('source', 'JANewsHelper');
            $source .= 'FP';
            if (class_exists($source)) {
                $obj = new $source();
                $this->articles = $obj->getArticles($this, $params);
                $this->total = $obj->getTotal($this, $params);
            	if($source == 'JAK2HelperFP'){
            		$k2catsid = $params->get('k2catsid');
            		if((count($k2catsid)==1 && $k2catsid['0']==0 ) || count($k2catsid) ==0){
            			$this->articles = null;
            			$this->total	= 0;
            		}
            	}
            }

        }


        /**
         * (non-PHPdoc)
         * @see JObject::get()
         * method get param of ja news featured module
         * @param string $name name of param
         * @param observe $default default value of param
         * @return observe value of param
         */
        function get($name, $default = null)
        {
            return $this->_params->get($name, $default);
        }


        /**
         * (non-PHPdoc)
         * @see JObject::set()
         * method set param of ja news featured module
         * @param string $name name of param
         * @param observe $value value for set to param
         * @return object param with new value
         */
        function set($name, $value)
        {
            return $this->_params->set($name, $value);
        }


        /**
         *
         * get file path
         * @param string $name name of file
         * @param string $modPath path of module
         * @param string $tmplPath path of template
         * @return string path of file
         */
        function getFile($name, $modPath, $tmplPath)
        {
            if (file_exists(JPATH_SITE . DS . $tmplPath . $name)) {
                return $tmplPath . $name;
            }
            return $modPath . $name;
        }


        /**
         *
         * Resize image in content
         * @param object $row
         * @param string $align
         * @param int $autoresize
         * @param int $maxchars
         * @param int $showimage
         * @param int $width
         * @param int $height
         * @param int $hiddenClasses
         * @return string new image
         */
        function replaceImage(&$row, $align, $autoresize, $maxchars, $showimage, $width = 0, $height = 0, $hiddenClasses = '')
        {
            $regex = '#<\s*img [^\>]*src\s*=\s*(["\'])(.*?)\1#im';

            preg_match($regex, $row->introtext, $matches);

            if (!count($matches)) {
                preg_match($regex, $row->text, $matches);
            }
            $images = (count($matches)) ? $matches : array();
            $image = '';
            if (count($images)) {
                $image = trim($images[2]);
            }
            $class = $align;
            $align = $align ? "align=\"$align\"" : "";

            if ($image && $showimage) {

                $thumbnailMode = $this->get('thumbnail_mode', 'crop');
                $aspect = $this->get('use_ratio', '1');
                $crop = $thumbnailMode == 'crop' ? true : false;

                $jaimage = JAImage::getInstance();

                if ($thumbnailMode != 'none' && $jaimage->sourceExited($image)) {
                    $imageURL = $jaimage->resize($image, $width, $height, $crop, $aspect);
                    $image = $imageURL ? "<img class=\"$class\" src=\"" . $imageURL . "\" alt=\"{$row->title}\" $align />" : "";
                } else {
                    $width = $width ? "width=\"$width\"" : "";
                    $height = $height ? "height=\"$height\"" : "";
                    $image = "<img class=\"$class\" src=\"" . $image . "\" alt=\"{$row->title}\" $width $height $align />";
                }

            } else {
                $image = '';
            }

            $regex1 = "/\<img[^\>]*>/";
            $row->introtext = preg_replace($regex1, '', $row->introtext);
            $regex1 = "/<div class=\"mosimage\".*<\/div>/";
            $row->introtext = preg_replace($regex1, '', $row->introtext);
            $row->introtext = trim($row->introtext);
            $row->introtext1 = $row->introtext;
            if ($maxchars && strlen($row->introtext) > $maxchars) {
                $doc = JDocument::getInstance();
                if (function_exists('mb_substr')) {
                    $row->introtext1 = SmartTrim::mb_trim($row->introtext, 0, $maxchars, $doc->_charset);
                } else {
                    $row->introtext1 = SmartTrim::trim($row->introtext, 0, $maxchars);
                }
            }

            // clean up globals
            return $image;
        }
    }
}

if (!class_exists('SmartTrim')) {
    /**
     * Smart Trim String Helper
     *
     */
    class SmartTrim
    {


        /**
         *
         * process string smart split
         * @param string $strin string input
         * @param int $pos start node split
         * @param int $len length of string that need to split
         * @param string $hiddenClasses show and redmore with property display: none or invisible
         * @param string $encoding type of string endcoding
         * @return string string that is smart splited
         */
        function mb_trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '', $encoding = 'utf-8')
        {
            mb_internal_encoding($encoding);
            $strout = trim($strin);

            $pattern = '/(<[^>]*>)/';
            $arr = preg_split($pattern, $strout, -1, PREG_SPLIT_DELIM_CAPTURE);
            $left = $pos;
            $length = $len;
            $strout = '';
            for ($i = 0; $i < count($arr); $i++) {
                /*$arr [$i] = trim ( $arr [$i] );*/
                if ($arr[$i] == '')
                    continue;
                if ($i % 2 == 0) {
                    if ($left > 0) {
                        $t = $arr[$i];
                        $arr[$i] = mb_substr($t, $left);
                        $left -= (mb_strlen($t) - mb_strlen($arr[$i]));
                    }

                    if ($left <= 0) {
                        if ($length > 0) {
                            $t = $arr[$i];
                            $arr[$i] = mb_substr($t, 0, $length);
                            $length -= mb_strlen($arr[$i]);
                            if ($length <= 0) {
                                $arr[$i] .= '...';
                            }

                        } else {
                            $arr[$i] = '';
                        }
                    }
                } else {
                    if (SmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
                        if ($endTag = SmartTrim::getCloseTag($arr, $i)) {
                            while ($i < $endTag)
                                $strout .= $arr[$i++] . "\n";
                        }
                    }
                }
                $strout .= $arr[$i] . "\n";
            }
            //echo $strout;
            return SmartTrim::toString($arr, $len);
        }


        /**
         *
         * process simple string split
         * @param string $strin string input
         * @param int $pos start node
         * @param int $len length of string that need to split
         * @param string $hiddenClasses show and redmore with property display: none or invisible
         * @return string
         */
        function trim($strin, $pos = 0, $len = 10000, $hiddenClasses = '')
        {
            $strout = trim($strin);

            $pattern = '/(<[^>]*>)/';
            $arr = preg_split($pattern, $strout, -1, PREG_SPLIT_DELIM_CAPTURE);
            $left = $pos;
            $length = $len;
            $strout = '';
            for ($i = 0; $i < count($arr); $i++) {
                /*$arr [$i] = trim ( $arr [$i] );*/
                if ($arr[$i] == '')
                    continue;
                if ($i % 2 == 0) {
                    if ($left > 0) {
                        $t = $arr[$i];
                        $arr[$i] = substr($t, $left);
                        $left -= (strlen($t) - strlen($arr[$i]));
                    }

                    if ($left <= 0) {
                        if ($length > 0) {
                            $t = $arr[$i];
                            $arr[$i] = substr($t, 0, $length);
                            $length -= strlen($arr[$i]);
                            if ($length <= 0) {
                                $arr[$i] .= '...';
                            }

                        } else {
                            $arr[$i] = '';
                        }
                    }
                } else {
                    if (SmartTrim::isHiddenTag($arr[$i], $hiddenClasses)) {
                        if ($endTag = SmartTrim::getCloseTag($arr, $i)) {
                            while ($i < $endTag)
                                $strout .= $arr[$i++] . "\n";
                        }
                    }
                }
                $strout .= $arr[$i] . "\n";
            }
            //echo $strout;
            return SmartTrim::toString($arr, $len);
        }


        /**
         * Check is Hidden Tag
         * @param string tag
         * @param string type of hidden
         * @return boolean
         */
        function isHiddenTag($tag, $hiddenClasses = '')
        {
            //By pass full tag like img
            if (substr($tag, -2) == '/>')
                return false;
            if (in_array(SmartTrim::getTag($tag), array('script', 'style')))
                return true;
            if (preg_match('/display\s*:\s*none/', $tag))
                return true;
            if ($hiddenClasses && preg_match('/class\s*=[\s"\']*(' . $hiddenClasses . ')[\s"\']*/', $tag))
                return true;
        }


        /**
         *
         * Get close tag from content array
         * @param array $arr content
         * @param int $openidx
         * @return int 0 if find not found OR key of close tag
         */
        function getCloseTag($arr, $openidx)
        {
            /*$tag = trim ( $arr [$openidx] );*/
            $tag = $arr[$openidx];
            if (!$openTag = SmartTrim::getTag($tag))
                return 0;

            $endTag = "<$openTag>";
            $endidx = $openidx + 1;
            $i = 1;
            while ($endidx < count($arr)) {
                if (trim($arr[$endidx]) == $endTag)
                    $i--;
                if (SmartTrim::getTag($arr[$endidx]) == $openTag)
                    $i++;
                if ($i == 0)
                    return $endidx;
                $endidx++;
            }
            return 0;
        }


        /**
         *
         * Get tag in content
         * @param string $tag
         * @return string tag
         */
        function getTag($tag)
        {
            if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches))
                return ''; //full tag
            if (preg_match('/\A<([^ \/>]*)([^>]*)>\Z/', trim($tag), $matches)) {
                //echo "[".strtolower($matches[1])."]";
                return strtolower($matches[1]);
            }
            //if (preg_match ('/<([^ \/>]*)([^\/>]*)>/', trim($tag), $matches)) return strtolower($matches[1]);
            return '';
        }


        /**
         *
         * convert array to string
         * @param array $arr
         * @param int $len
         * @return string
         */
        function toString($arr, $len)
        {
            $i = 0;
            $stack = new JAStack();
            $length = 0;
            while ($i < count($arr)) {
                /*$tag = trim ( $arr [$i ++] );*/
                $tag = $arr[$i++];
                if ($tag == '')
                    continue;
                if (SmartTrim::isCloseTag($tag)) {
                    if ($ltag = $stack->getLast()) {
                        if ('</' . SmartTrim::getTag($ltag) . '>' == $tag)
                            $stack->pop();
                        else
                            $stack->push($tag);
                    }
                } else if (SmartTrim::isOpenTag($tag)) {
                    $stack->push($tag);
                } else if (SmartTrim::isFullTag($tag)) {
                    //echo "[TAG: $tag, $length, $len]\n";
                    if ($length < $len)
                        $stack->push($tag);
                } else {
                    $length += strlen($tag);
                    $stack->push($tag);
                }
            }

            return $stack->toString();
        }


        /**
         *
         * Check is open tag
         * @param string $tag
         * @return boolean
         */
        function isOpenTag($tag)
        {
            if (preg_match('/\A<([^\/>]+)\/>\Z/', trim($tag), $matches))
                return false; //full tag
            if (preg_match('/\A<([^ \/>]+)([^>]*)>\Z/', trim($tag), $matches))
                return true;
            return false;
        }


        /**
         *
         * Check is full tag
         * @param string $tag
         * @return boolean
         */
        function isFullTag($tag)
        {
            //echo "[Check full: $tag]\n";
            if (preg_match('/\A<([^\/>]*)\/>\Z/', trim($tag), $matches))
                return true; //full tag
            return false;
        }


        /**
         *
         * Check is close tag
         * @param string $tag
         * @return boolean
         */
        function isCloseTag($tag)
        {
            if (preg_match('/<\/(.*)>/', $tag))
                return true;
            return false;
        }
    }
}
if (!class_exists('JAStack')) {

    /**
     * News Pro Module JAStack Helper
     */
    class JAStack
    {
        /*
         * array
         */
        var $_arr = null;


        /**
         * Constructor
         *
         * For php4 compatability we must not use the __constructor as a constructor for plugins
         * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
         * This causes problems with cross-referencing necessary for the observer design pattern.
         *
         */
        function JAStack()
        {
            $this->_arr = array();
        }


        /**
         *
         * Push item value into array
         * @param observe $item value of item that will input to stack
         * @return unknown
         */
        function push($item)
        {
            $this->_arr[count($this->_arr)] = $item;
        }


        /**
         *
         * Pop item value from array
         * @param observe $item value of item that will pop from stack
         * @return unknow value of item that is pop from array
         */
        function pop()
        {
            if (!$c = count($this->_arr))
                return null;
            $ret = $this->_arr[$c - 1];
            unset($this->_arr[$c - 1]);
            return $ret;
        }


        /**
         *
         * Get value of last element in array
         * @return unknown value of last element in array
         */
        function getLast()
        {
            if (!$c = count($this->_arr))
                return null;
            return $this->_arr[$c - 1];
        }


        /**
         *
         * Convert array to string
         * @return string
         */
        function toString()
        {
            $output = '';
            foreach ($this->_arr as $item) {
                $output .= $item;
            }
            return $output;
        }
    }
}


