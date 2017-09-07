<?php
/*
 * ------------------------------------------------------------------------
 * JA Teline IV Template for Joomla 2.5
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 

?>
<?php
$positions = preg_split ('/,/', T3Common::node_data($block));
$parent = T3Common::node_attributes($block, 'parent', 'middle');
$style = $this->getBlockStyle ($block, $parent);
if (!$this->countModules (T3Common::node_data($block))) return;
$pos = array();
//$active = JRequest::getVar(T3Common::node_attributes($block, 'name', 'tabs')."-active-position", '', 'COOKIE');
$active = JRequest::getVar(T3_ACTIVE_TEMPLATE.'_t3custom_'.T3Common::node_attributes($block, 'name', 'tabs')."-active-position", '', 'COOKIE');
foreach ($positions as $position) {
	if ($this->countModules($position)) {
		$pos[] = $position;
	}
}
if (!in_array($active, $pos)) $active = $pos[0];
?>

<div class="ja-blocktab">

<?php if (count($pos) > 1): ?>
<div class="ja-blocktab-title clearfix">	
	<ul>
	<?php foreach ($pos as $position) : ?>
		<li class="blocktab-<?php echo $position ?> <?php if ($active==$position) echo "active" ?>" onclick="jaswitchtab ('<?php echo $position ?>')"><span><?php echo JText::_($position) ?></span></li>
	<?php endforeach ?>
	</ul>
</div>
<?php if (!defined('_SWITCH_TAB_')): 
	define ('_SWITCH_TAB_', 1);
?>
<script type="text/javascript">
	function jaswitchtab (tab) {
		//createCookie('<?php echo T3Common::node_attributes($block, 'name', 'tabs')."-active-position" ?>', tab, 365);
		createCookie('<?php echo T3_ACTIVE_TEMPLATE.'_t3custom_'.T3Common::node_attributes($block, 'name', 'tabs')."-active-position" ?>', tab, 365);
		window.location.reload();
	}
</script>
<?php endif ?>
<?php endif ?>

<div class="ja-blocktab-content clearfix">	
	<jdoc:include type="modules" name="<?php echo $active ?>" style="<?php echo $style ?>" />	
</div>

</div>