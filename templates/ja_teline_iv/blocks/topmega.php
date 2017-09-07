<?php
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

//Top navigation - megamenu
$topmenu = null;
$topparams = new JParameter('');
$topparams->set( 'menutype', 'topmega' );
$topparams->set( 'menu_images_align', 'left' );
$topparams->set('menu_title', 0);
$topparams->set('menuname', 'ja-topmega'); //to generate id for this menu

//$topmenu = $this->loadMenu($topparams, 'mega'); 
$file = JPATH_BASE.DS.T3_CORE.DS.'menu'.DS."mega.class.php";
if (!is_file ($file)) return null;
require_once ($file);
$menuclass = "JAMenumega";
$topmenu = new $menuclass ($topparams);
//assign template object
$topmenu->_tmpl = $this;
//load menu
$topmenu->loadMenu();
if($topparams->get('menutype','mainmenu') =='topmega'): 
//check css/js file
$this->addCSS ('css/menu/mega.css');
$this->addJS ('js/menu/mega.js');
?>
<?php $this->genBlockBegin ($block) ?>
<?php //var_dump($topmenu);?>
<div id="ja-topnav" class="clearfix">
	<?php if (($topmenu)) $topmenu->genMenu (); ?>
</div>
<?php $this->genBlockEnd ($block) ?>

<?php endif;?>