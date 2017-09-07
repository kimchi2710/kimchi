<?php
/**
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





<table id="ja-info" cellpadding="0" cellspacing="0"  width="100%">	
	<tr class="level1">
		<td class="ja-block-head">
			<h4 id="ja-head-additionalinformation" class="block-head block-head-additionalinformation open" rel="1" >
				<span class="block-setting" ><?php echo JText::_('Additional Information')?></span> 
				<span class="icon-help editlinktip hasTip" title="<?php echo JText::_('Additional Information')?>::<?php echo sprintf(JText::_('Additional Information desc'), strtoupper($template))?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<a class="toggle-btn open" title="<?php echo JText::_('Expand all')?>" onclick="showRegion('ja-head-additionalinformation', 'level1'); showRegion('ja-info-more', 'level1'); return false;"><?php echo JText::_('Expand all')?></a>
				<a class="toggle-btn close" title="<?php echo JText::_('Collapse all')?>" onclick="hideRegion('ja-head-additionalinformation', 'level1'); hideRegion('ja-info-more', 'level1'); return false;"><?php echo JText::_('Collapse all')?></a>
			</h4>
		</td>
    </tr>
</table>
<div id="ja-info-more">
		<div style="text-align: left; padding-right: 4px;">
				<ol class="ja-quicklinks clearfix">
					<li><a href="http://www.joomlart.com/forums/forumdisplay.php?10233-JA-Teline-IV-Guides-Tutorials-Tips" target="_blank" title="Template userguide">Template userguide</a></li>
					<li><a href="http://www.joomlart.com/demo/#templates.joomlart.com/ja_teline_iv" target="_blank" title="Template Live Demo">Template Live Demo</a></li>
					<li><a href="http://www.joomlart.com/forums/forumdisplay.php?233-JA-Teline-IV" target="_blank" title="Report Bug">Report Bug</a></li>
					<li><a href="http://update.joomlart.com/#products.list/template/JA%20Teline%20IV%20template/" target="_blank" title="Check Version">Check Version</a></li>
					<li><a href="http://www.joomlart.com/joomla/templates/ja-teline-iv" target="_blank" title="JA Teline IV - Joomla! Magazine / News Template - supports K2">JA Teline IV - Joomla! Magazine / News Template - supports K2</a></li>
				</ol>
			</div>
</div>	

