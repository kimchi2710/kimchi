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
$bigitems 				= intval (trim( $helper->get( 'bigitems', 1) ));
$numberofhead			= intval (trim( $helper->get( 'numberofheadlinenews', 10) ));
$animType				= $helper->get ( 'animType', 'animNewsMoveHor');
$autoplay				= $helper->get ( 'autoplay', 1);
$duration				= (int)$helper->get('duration', 400);
$autoplay_duration		= (int)$helper->get('autoplay_duration', 3);
$showtooltip			= 	intval (trim( $helper->get( 'showtooltip', 1 ) ));
$i = 0;

$normalitems = intval($numberofhead-$bigitems-10);
if (($normalitems > 3) || ($normalitems < 3)) {
	$normalitems = 3;
}
?>
<?php if(count($rows) > 0) : ?>
<?php
if (!defined ('_MODE_JANEWS_FP_ASSETS_DEFAULT')) {
	define ('_MODE_JANEWS_FP_ASSETS_DEFAULT', 1);
	if (JFolder::exists('templates/'.$app->getTemplate().'/html/'.$module->module.'/'.$theme.'/')){
		JHTML::script('script.js','templates/'.$app->getTemplate().'/html/'.$module->module.'/'.$theme.'/');
	}
	else {
		JHTML::script('script.js','modules/'.$module->module.'/tmpl/'.$theme.'/');
	}
}?>
<!--  -->

<div id="ja-zinfp-wrap-<?php echo $module->id?>" class="ja-zinfp-wrap <?php echo $theme?>">
  <div id="ja-zinfp-<?php echo $module->id?>" class="ja-zinfp clearfix">
    <div class="ja-zinfp-main-wrap">
      <div class="ja-zinfp-main clearfix">
        <div class="ja-zinfp-featured-wrap column">
          <div class="ja-zinfp-featured clearfix">
			
            <?php for ($t=0; $t<count($rows) && $t<$bigitems; $t++){?>
            <?php $news = $rows[$t]?>
            <div class="ja-zincontent-wrap <?php if($i==0){ echo 'active show';} else{ echo 'hide';}?>">
              <div class="ja-zincontent clearfix">
                <?php if($bigshowimage)	echo $news->bigimage?>
                <h2 class="ja-zintitle"> <a href="<?php echo $news->link;?>" title="<?php echo strip_tags($news->title); ?>"> <?php echo $news->title;?> </a> </h2>
                <?php echo $bigmaxchar > strlen($news->bigintrotext)?$news->introtext:$news->bigintrotext?>
                <?php if ($showhlreadmore) {?>
                <p class="readmore"> <a href="<?php echo $news->link?>" class="readon" title="<?php echo JText::_('JAFP_READ_MORE');?>"><span><?php echo JText::_('JAFP_READ_MORE');?></span></a> </p>
                <?php } ?>
              </div>
            </div>
            <?php } ?>
			
          </div>
        </div>
        <div class="ja-zinfp-normal-wrap column">
          <div class="ja-zinfp-normal clearfix">
            <?php for ($t=$bigitems; $t<count($rows) && $t<=$normalitems; $t++){?>
            <?php $news = $rows[$t]?>
            <div class="ja-zincontent-wrap">
              <div class="ja-zincontent clearfix">
                <?php if($smallshowimage)	echo $news->smallimage?>
                <h4 class="ja-zintitle"> <a href="<?php echo $news->link;?>" title="<?php echo strip_tags($news->title); ?>"> <?php echo $news->title;?> </a> </h4>
              </div>
            </div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
    <div class="ja-zinfp-links-wrap column">
      <div class="ja-zinfp-links clearfix">
        <?php for ($t=$normalitems+1; $t<count($rows); $t++){?>
        <?php $news = $rows[$t]?>
        <div class="ja-zincontent-wrap">
          <div class="ja-zincontent clearfix <?php if($showtooltip){?>editlinktip jahasTip<?php }?>" <?php if($showtooltip){?>title="<?php echo trim(strip_tags($news->title), '"'); ?>::<?php echo htmlspecialchars($news->smallimage.$news->bigintrotext)?>"<?php }?>>
            <h4 class="ja-zintitle"> <a href="<?php echo $news->link;?>" title="<?php echo strip_tags($news->title); ?>"> <?php echo $news->title;?> </a> </h4>
          </div>
        </div>
        <?php } ?>
        <!--div class="ja-zinfp-links-actions"> <a class="ja-zinfp-links-actions-prev prev" href="javascript:void(0)"> <span>Prev</span> </a> <a class="ja-zinfp-links-actions-next next" href="javascript:void(0)"> <span>Next</span> </a> <span class="next"><img alt="Loading" src="loading.gif"> </span> </div-->
      </div>
    </div>
  </div>
</div>
<?php endif; ?>