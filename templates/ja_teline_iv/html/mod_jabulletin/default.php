<?php
/*
 * ------------------------------------------------------------------------
 * JA Teline IV
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - Copyrighted Commercial Software
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites:  http://www.joomlart.com -  http://www.joomlancers.com
 * This file may not be redistributed in whole or significant part.
 * ------------------------------------------------------------------------
*/ 

defined('_JEXEC') or die('Restricted access'); 
$rtl = 0;
$doc =& JFactory::getDocument();
if($doc->direction=='rtl') $rtl = 1;
$padding_prop = $rtl?'padding-right':'padding-left';
?>
<div class="ja-bulletin-wrap">
	<ul class="ja-bulletin<?php echo $params->get('moduleclass_sfx'); ?> clearfix">
	<?php if(!empty($list)): ?>
	<?php foreach ($list as $item) : ?>
		<li class="clearfix">
				<?php 
				$padding = ($params->get( 'show_image'))?"style=\"$padding_prop:".($params->get('width')+10)."px\"":"";?>
				<?php
				if (isset($item->image) && $item->image) : 
				?>
				<div class="box-left">
			
				<?php if( $item->image ) : ?>
					<a href="<?php echo $item->link; ?>" class="mostread-image">
						<?php echo $item->image; ?>
					</a>
				<?php endif; ?>
				
				</div>
				<?php endif; ?>
				<div <?php echo $padding;?> class="box-right">
				<a href="<?php echo $item->link; ?>" class="mostread"><?php echo $item->text; ?></a>
				<br/>
				
					<span class="post-date">	
						<?php if ($showcreater) : ?>
					<span class="createby"><?php echo JText::_('By: '); ?><span><?php echo $item->creater;?></span></span>
				<?php endif;?>				
				
				<?php if (isset($item->date)) :?>
					<span class="createdate"><?php echo JText::_('Post').': '; echo JHTML::_('date', $item->date, JText::_('DATE_FORMAT_LC4')); ?></span>
				<?php endif;?>
				</span>
            <?php if (isset($item->hits)) : ?>
					<span class="hits">
					<?php if($useCustomText):
						 echo JText::_($customText);
					endif;
					?>
					<?php echo $item->hits; ?>
					</span>
				<?php endif; ?>
            <?php if ($showreadmore) : ?>
					<a href="<?php echo $item->link; ?>" class="readon" title="<?php echo JText::sprintf('READ_MORE');?>"><?php echo JText::sprintf('READ_MORE');?></a>
				<?php endif; ?>	
				</div>
		</li>
	<?php endforeach; ?>
	<?php endif; ?>
	</ul>
</div>