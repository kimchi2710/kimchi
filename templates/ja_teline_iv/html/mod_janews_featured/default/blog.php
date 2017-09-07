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

defined('_JEXEC') or die('Restricted access');
/* This template for headline (frontpage): first news with big image and next news with smaller images
bigimg_w, bigimg_h, smallimg_w, smallimg_h
*/
$showhlreadmore 		= 	intval (trim( $helper->get( 'showhlreadmore', 0 ) ));
$align 		= 	 $helper->get( 'align', 0 );
//if($align==0) $align="none";
$bigmaxchar 			= $helper->get ( 'bigmaxchars', 200 );
$bigshowimage 			= $helper->get ( 'bigshowimage', 1 );
$smallmaxchar 			= $helper->get ( 'smallmaxchars', 100 );
$smallshowimage 		= $helper->get ( 'smallshowimage', 1 );
$bigitems 				= intval (trim( $helper->get( 'bigitems', 1) )) > 0 ? intval (trim( $helper->get( 'bigitems', 1) )) : 1 ;
$animType				= $helper->get ( 'animType', 'animNewsMoveHor');
$autoplay				= $helper->get ( 'autoplay', 1);
$duration				= (int)$helper->get('duration', 400);
$autoplay_duration		= (int)$helper->get('autoplay_duration', 3);
$showtooltip			= 	intval (trim( $helper->get( 'showtooltip', 1 ) ));	
$i = 0;
?>
<?php if(count($rows) > 0) : ?>
<?php 
if (!defined ('_MODE_JANEWS_FP_ASSETS_DEFAULT')) {
	define ('_MODE_JANEWS_FP_ASSETS_DEFAULT', 1);
	JHTML::script('script.js','modules/'.$module->module.'/tmpl/'.$theme.'/');
}?><!--  -->

<div id="ja-zinfp-wrap-<?php echo $module->id?>" class="ja-zinfp-wrap <?php echo $theme?>">
<div id="ja-zinfp-<?php echo $module->id?>" class="ja-zinfp clearfix">

	<div class="ja-zinfp-main-wrap">
	<div class="ja-zinfp-main clearfix">
		<div class="ja-zinfp-featured-wrap column">
		<div class="ja-zinfp-featured-border">
		<div class="ja-zinfp-featured clearfix">
		
		<?php foreach ($rows as $news) {
		if($i<$bigitems) {?>
			<div class="ja-zincontent-wrap <?php if($i==0){ echo 'active show';} else{ echo 'hide';}?>">
			<div class="ja-zincontent clearfix">
				<?php if($bigshowimage && $news->bigimage) {?>
				<div class="ja-zincontent-img clearfix" <?php if ($align!=0) {?> style="float:<?php echo $align; ?>" <?php } ?>>
				<?php if($bigshowimage)	echo $news->bigimage?>
				</div>
				<?php } ?>
				<h2 class="ja-zintitle">
					<a href="<?php echo $news->link;?>" title="<?php echo strip_tags($news->title); ?>">
						<?php echo $news->title;?>
					</a>
				</h2>
				
				<?php echo $bigmaxchar > strlen($news->bigintrotext)?$news->introtext:$news->bigintrotext?>
				
				<?php if ($showhlreadmore) {?>
				<p class="readmore">
					<a href="<?php echo $news->link?>" class="readon" title="<?php echo JText::_('READ_MORE');?>"><span><?php echo JText::_('READ_MORE');?></span></a>
				</p>
				<?php } ?>
			</div>
			</div>
			
			<?php if($i==$bigitems-1){?>
		</div>
		<?php if($bigitems>1 && count($rows)>1){?>
			<div class="ja-zinfp-links-actions">
				<span class="box-counter"><span class="counter">1</span> <?php echo JText::_('of')?> <?php echo $bigitems?></span>
				<a href="javascript:void(0)" class="ja-zinfp-links-actions-next next">
					<span><?php echo JText::_('Next')?></span>					
				</a>
				<a href="javascript:void(0)" class="ja-zinfp-links-actions-prev prev">
					<span><?php echo JText::_('Prev')?></span>
				</a>
				<span class="next"><img src="modules/mod_janews_featured/tmpl/default/loading.gif" alt="Loading"/></span>
			</div>
			<?php }?>
		</div>
		</div>

		<div class="ja-zinfp-normal-wrap column">
		<div class="ja-zinfp-normal clearfix">
			<?php }?>
		<?php }else{?>	
			<div class="ja-zincontent-wrap">
				<div class="ja-zincontent clearfix <?php if($showtooltip){?>editlinktip jahasTip<?php }?>" <?php if($showtooltip){?>title="<?php echo trim(strip_tags($news->title), '"'); ?>::<?php echo htmlspecialchars($news->smallimage.$news->bigintrotext)?>"<?php }?>>
					<?php if($smallshowimage && $news->smallimage) {?>
					<div class="ja-zincontent-img <?php echo $align;  ?> " <?php if ($align!=0) {?> style="float:<?php echo $align; ?>" <?php } ?>>
					<?php echo $news->smallimage;?>
					</div>
					<?php } ?>

					<h4 class="ja-zintitle">
						<a href="<?php echo $news->link;?>" title="<?php echo strip_tags($news->title); ?>">
							<?php echo $news->title;?>
						</a>
					</h4>
					<div class="ja-zinintro">
						<?php echo $smallmaxchar > strlen($news->smallintrotext)?$news->introtext:$news->smallintrotext?>
					</div>

				</div>
			</div>
			<?php }?>
		<?php ++$i?>
		<?php }?>
		
		</div>
		</div>
	</div>
	</div>

</div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
	window.addEvent('load', function() {		
		new JANEWSFP_DEFAULT({'moduleid': <?php echo $module->id?>, 'animType': '<?php echo $animType?>', 'duration': <?php echo $duration?>, 'autoplay':<?php echo $autoplay?>, 'autoplay_duration':<?php echo $autoplay_duration?>});
		$$('.ja-zincontent-wrap').removeClass('hide').removeClass('show');
	});
/* ]]> */	
</script>
<?php endif; ?>