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
	$app = & JFactory::getApplication();
	$siteName = $app->getCfg('sitename');
	?>
	<div id="ja-footlogo" >
		<a href="index.php" title="<?php echo $siteName; ?>"><span><?php echo $siteName; ?></span></a>
	</div>

	<?php if($this->countModules('footnav')) : ?>
	<div class="ja-footnav">
		<jdoc:include type="modules" name="footnav" />
		<?php
		//detect view on mobile - show switch to mobile tools
		$layout_switcher = $this->loadBlock('usertools/layout-switcher');
		if ($layout_switcher) {
			$layout_switcher = '<li class="layout-switcher">'.$layout_switcher.'</li>';
		}
		?>
		<ul class="ja-links">
			<?php echo $layout_switcher ?>
			<li class="top"><a href="<?php echo $this->getCurrentURL();?>#Top" title="<?php echo JText::_("BACK_TO_TOP") ?>"><?php echo JText::_('TOP') ?></a></li>
		</ul>
		
		<ul class="no-display">
			<li><a href="<?php echo $this->getCurrentURL();?>#ja-content" title="<?php echo JText::_("SKIP_TO_CONTENT");?>"><?php echo JText::_("SKIP_TO_CONTENT");?></a></li>
		</ul>
	</div>
	<?php endif; ?>
	
	<div class="ja-copyright">
		<jdoc:include type="modules" name="footer" />
	</div>

	<?php 
	$t3_logo = $this->getParam ('setting_t3logo', 't3-logo-light', 't3-logo-dark');
	if ($t3_logo != 'none') : ?>
	<div id="ja-poweredby" class="<?php echo $t3_logo ?>">
		<a href="http://t3.joomlart.com" title="Powered By T3 Framework" target="_blank"><span>Powered By T3 Framework</span></a>
	</div>  	
	<?php endif; ?>
