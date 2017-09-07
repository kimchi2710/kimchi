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
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 


$headlinelang 			= 	trim( $helper->get( 'headlinelang', JText::_('') ));
$headlineheight 		= 	intval ($helper->get( 'headlineheight', '' ));
$numberofheadlinenews 	= 	intval (trim( $helper->get( 'numberofheadlinenews', 10 ) ));
$numberofheadlinenews 	= ($numberofheadlinenews < count($rows)) ? $numberofheadlinenews : count($rows);
$delaytime 				= 	intval (trim( $helper->get( 'delaytime', 5 ) ));
$autoroll 				= 	intval (trim( $helper->get( 'autoroll', 1) ));
$showhlreadmore 		= 	intval (trim( $helper->get( 'showhlreadmore', 1 ) ));
$showhltitle 			= 	intval (trim( $helper->get( 'showhltitle', 1 ) ));
$showhltools 			= 	intval (trim( $helper->get( 'showhltools', 1 ) ));
$bigshowimage 			= $helper->get ( 'bigshowimage', 1 );
$bigmaxchar 			= $helper->get ( 'bigmaxchars', 1 );
if(count($rows) > 0) : 
if ($numberofheadlinenews >= 1) {
	if (isset($_COOKIE['JAHL-AUTOROLL'])) $autoroll = ($_COOKIE['JAHL-AUTOROLL']) ? 1 : 0;
	setcookie("JAHL-AUTOROLL", $autoroll, 0, "/");

	if (!defined ('_MODE_JAMEGAMENU_FP_ASSETS_HEADLINE')) {
		define ('_MODE_JAMEGAMENU_FP_ASSETS_HEADLINE', 1);
		
		if (JFolder::exists('templates/'.$app->getTemplate().'/html/'.$module->module.'/'.$theme.'/')){
			JHTML::script('script.js','templates/'.$app->getTemplate().'/html/'.$module->module.'/'.$theme.'/');
		}
		else {
			JHTML::script('script.js','modules/'.$module->module.'/tmpl/'.$theme.'/');
		}
		
	}


	?>
	<script type="text/javascript">
	/* <![CDATA[ *//* ]]> */
		var jaNewsHL = new JA_NewsHeadline({
				autoroll: <?php echo intval($autoroll || !$showhltools);?>,
				total: <?php echo $numberofheadlinenews;?>,
				delaytime: <?php echo $delaytime;?>,
				moduleid: <?php echo $module->id?>
		});
		window.addEvent('domready', function() {
			jaNewsHL.start();
		});
	/* ]]> */
	</script>

<?php }?>
<?php $pauseplay = $autoroll?'Pause':'Play';?>

<div id="ja-newshlcache-<?php echo $module->id?>" class="ja-newshlcache <?php echo $theme?>" style="display: none">
	
	<?php foreach ($rows as $news) {?>
		<div class="ja-zincontent">
			<div class="inner clearfix">

				<?php if($bigshowimage) echo $news->bigimage?>

				<?php if ($showhltitle) { ?>
				<h4 class="ja-zintitle">
					<a href="<?php echo $news->link?>" class="ja-newstitle" title="<?php echo strip_tags($news->title); ?>">
						<?php echo $news->title;?>
					</a>
				</h4>
				<?php } ?>

				<?php echo $bigmaxchar > strlen($news->bigintrotext)?$news->introtext:$news->bigintrotext?>

				<?php if ($showhlreadmore) {?>
				<a href="<?php echo $news->link?>" class="readon" title="<?php echo JText::_('JAFP_READ_MORE');?>"><span><?php echo JText::_('JAFP_READ_MORE');?></span></a>
				<?php } ?>

			</div>
		</div>
	<?php }?>
	
</div>

<div id="ja-zinhlwrap-<?php echo $module->id?>" class="ja-zinhlwrap  <?php echo $theme?>">
	<div class="ja-zinhl clearfix" style="width: 100%;<?php echo ($headlineheight ? " height: {$headlineheight}px; overflow: hidden;" : ""); ?>">
		<div class="ja-zinhlinner" style="width: 100%;">
		<?php if($showhltools || $headlinelang) {?>
			<div class="ja-zinsec clearfix">
				<?php if($headlinelang) {?><h2><?php echo $headlinelang;?></h2><?php }?>

				<?php if($showhltools) {?>
					<?php $button_image 	 = $helper->getFile( strtolower($pauseplay).'.png','modules/mod_janews_featured/assets/images/','templates/'.$mainframe->getTemplate().'/images/');?>
					<?php $button_image_prev = $helper->getFile('prev.png','modules/mod_janews_featured/assets/images/','templates/'.$mainframe->getTemplate().'/images/'); ?>
					<?php $button_image_next = $helper->getFile('next.png','modules/mod_janews_featured/assets/images/','templates/'.$mainframe->getTemplate().'/images/'); ?>
					<div class="ja-zinhl-controlbar">
						<ul>
							<li><img title="<?php echo $pauseplay;?>" style="cursor: pointer;" id="jahl-switcher-<?php echo $module->id?>" onclick="jaNewsHL.toogle(); return false;" src="<?php echo $button_image?>" alt="<?php echo $pauseplay;?>" border="0" /></li>
							<li><img title="" style="cursor: pointer;" onclick="jaNewsHL.prev(); return false;" id="jahl-prev-<?php echo $module->id?>" src="<?php echo $button_image_prev?>" alt="Previous" border="0" /></li>
							<li><img title="" style="cursor: pointer;" onclick="jaNewsHL.next(); return false;" id="jahl-next-<?php echo $module->id?>" src="<?php echo $button_image_next?>" alt="Next" border="0" /></li>
						</ul>
						<span id="ja-zinhl-counter-<?php echo $module->id?>" class="ja-zinhl-counter">1/<?php echo $numberofheadlinenews;?></span>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
			<div id="ja-zinhl-newsitem-<?php echo $module->id?>"></div>
		</div>
	</div>
</div>
<?php endif; ?>
