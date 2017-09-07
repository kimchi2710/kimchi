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
/* This template for headline (frontpage): first news with big image and next news with smaller images
bigimg_w, bigimg_h, smallimg_w, smallimg_h
*/
$showhlreadmore 		= 	intval (trim( $helper->get( 'showhlreadmore', 0 ) ));
$bigmaxchar 			= $helper->get ( 'bigmaxchars', 200 );
$bigshowimage 			= $helper->get ( 'bigshowimage', 1 );
$smallmaxchar 			= $helper->get ( 'smallmaxchars', 100 );
$smallshowimage 		= $helper->get ( 'smallshowimage', 1 );
$bigitems 				= intval (trim( $helper->get( 'bigitems', 1) ));
$showtooltip 			= 	intval (trim( $helper->get( 'showtooltip', 1 ) ));
$i = 0;
?>
<?php if(count($rows) > 0) : ?>

<div id="jazin-hlwrap-<?php echo $module->id?>" class="clearfix <?php echo $theme?>">

	<div id="jazin-hlfirst-<?php echo $module->id?>">
	
	<?php foreach ($rows as $news) {
	//$news->bigimage = '';
	
	$toottips_image = $news->bigimage;
	$toottips_image = preg_replace('/src="*/', 'src="'.JURI::base().'$1', $toottips_image);

	if($i<$bigitems) {?>
		<div class="jazin-contentwrap">
			<div class="jazin-content clearfix <?php if($showtooltip){?>editlinktip hasTip<?php }?>" <?php if($showtooltip){?>title="<?php echo trim(strip_tags($news->title), '"'); ?>::<?php echo htmlspecialchars($toottips_image.$news->introtext);?>"<?php }?>>			
					<a href="<?php echo $news->link;?>"  class="jazin-content-inner">
						<?php if($bigshowimage)	echo $news->bigimage?>
					
						<span class="jazin-content-text">
							<span class="jazin-title"><?php echo $news->title;?></span>						
							<?php echo $bigmaxchar > strlen($news->bigintrotext)?$news->introtext:$news->bigintrotext?>
						</span>
						<?php if ($showhlreadmore) {?><span><?php echo JText::_('READ MORE...');?></span><?php } ?>
					</a>
											
			</div>
		</div>
		
 		<?php if($i==$bigitems-1){?>
	</div>
	<div id="jazin-hlnext-<?php echo $module->id?>">
		<?php }?>
	<?php }else{?>	
		<div class="jazin-contentwrap">
			<div class="jazin-content clearfix <?php if($showtooltip){?>editlinktip hasTip<?php }?>" <?php if($showtooltip){?>title="<?php echo trim(strip_tags($news->title), '"'); ?>::<?php echo htmlspecialchars($news->bigimage.$news->introtext);?>"<?php }?>>
				<?php if($smallshowimage)	echo $news->smallimage?>
				<h4 class="jazin-title">
					<a href="<?php echo $news->link;?>">
						<?php echo $news->title;?>
					</a>
				</h4>
				<?php echo $smallmaxchar > strlen($news->smallintrotext)?$news->introtext:$news->smallintrotext?>
				<?php if ($showhlreadmore) {?>
				<p class="readmore">
					<a href="<?php echo $news->link?>" class="readon" title="<?php echo JText::_('READ MORE...');?>"><span><?php echo JText::_('READ MORE...');?></span></a>
				</p>
				<?php } ?>
			</div>
		</div>
		<?php }?>
	
	<?php ++$i?>
	
<?php }?>

</div>

</div>

<span class="article_separator">&nbsp;</span>

<?php endif; ?>
