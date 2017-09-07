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
/**
 * JA News Sticker module allows display of article's title from sections or categories. \
 * You can configure the setttings in the right pane. Mutiple options for animations are also added, choose any one.
 * If you are using this module on Teline III template, * then the default module position is "headlines".
 **/
  // no direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if( $params->get('show_headtext', 1) ) : ?>
<div class="ja-healineswrap">
	<?php $headtext = $params->get('headtext', 'Headlines:'); 
		$headtext = (strlen($headtext) > 255) ? substr( $headtext, 0, 255 ) : $headtext;
		if(strlen($headtext)==255){
			$headtext = substr($headtext, 0, strrpos($headtext, ' '));
		}
	?>
<?php if( $params->get('show_buttons_control',0) ) : ?>	
<div class="ja-headelines-buttons">
	<a class="ja-headelines-pre" onclick="return false;" href=""><span><?php echo JText::_("PREVIOUS");?></span></a>
    <a class="ja-headelines-next" onclick="return false;" href=""><span><?php echo JText::_("NEXT");?></span></a> 
</div>
<?php endif; ?>	
	<em><?php echo $headtext; ?></em>
<?php endif; ?>
	<div   id="<?php echo $moduleID; ?>" class="ja-headlines <?php echo $params->get('moduleclass_sfx');?>">
		<div style="white-space:nowrap; " id="jahl-wapper-items-<?php echo $moduleID; ?>">
		<!-- HEADLINE CONTENT -->
		<?php if( isset($list) && !empty($list) ) : ?>
		<?php foreach( $list as $index => $item ) : ?>
				<div class="ja-headlines-item <?php echo 'jahl-'.$animationType ;?>" style="visibility:<?php echo ($index !=0) ?'hidden':'visible' ?>">
					<a  title="<?php echo   modJANewStickerHelper::trimString( trim(strip_tags($item->introtext) ),  300); ?>" <?php echo $aClass ;?> href="<?php echo modJANewStickerHelper::getLink($item, $userRSS); ?>"><span><?php echo modJANewStickerHelper::trimString( $item->title, $titleMaxChars );?></span>
					</a> 
				</div>
		<?php  endforeach; // endforeach; ?>
		<?php endif ; ?>
		<!-- //HEADLINE CONTENT -->
		</div>	
	</div>
<?php if( $params->get('show_headtext', 1 ) ) : ?>	
</div>
<?php endif; ?>