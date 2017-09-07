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

	<p class="ja-day clearfix">
	  <?php 
		echo "<span class=\"day\">".JText::_(strtoupper(date ('D')))."</span>";
		echo "<span class=\"month\">".date ('m')."</span>";
		echo "<span class=\"date\">".date ('d')."</span>";
		echo "<span class=\"year\">".date ('Y')."</span>";
	  ?>
	</p>
	 
	<p class="ja-updatetime"><span><?php echo JText::_('LAST_UPDATE')?></span><em><?php echo T3Common::getLastUpdate(); ?></em></p>
	
	<?php if($this->countModules('headlines')) : ?>
		<jdoc:include type="modules" name="headlines" />
	<?php endif; ?>
	
	<?php if($this->countModules('search')) : ?>
	<div id="ja-search">
		<jdoc:include type="modules" name="search" />
	</div>
	<?php endif; ?>
	
