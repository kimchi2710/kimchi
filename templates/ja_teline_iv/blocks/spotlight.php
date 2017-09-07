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
//$spotlight = array ('user1','user2','user3','user4','user5');
$spotlight = preg_split ('/,/', T3Common::node_data($block));
$name = T3Common::node_attributes($block, 'name');
$special = T3Common::node_attributes($block, 'special');
$specialwidth = T3Common::node_attributes($block, 'specialwidth');
$totalwidth = T3Common::node_attributes($block, 'totalwidth', 100);
$style = $this->getBlockStyle ($block);
$botsl = $this->calSpotlight ($spotlight,$totalwidth, $specialwidth, $special);
if( $botsl ) :
?>

	<!-- SPOTLIGHT -->
	<?php foreach ($spotlight as $pos): ?>
	<?php if( $this->countModules($pos) ): ?>
	<div class="ja-box-wrap column ja-box<?php echo $botsl[$pos]['class']; ?>" style="width: <?php echo $botsl[$pos]['width']; ?>;">
	<div class="ja-box clearfix">
		<jdoc:include type="modules" name="<?php echo $pos ?>" style="<?php echo $style ?>" />
	</div>
	</div>
	<?php endif; ?>
	<?php endforeach ?>
	<!-- SPOTLIGHT -->

<script type="text/javascript">
	window.addEvent('load', function (){ equalHeight ('#ja-<?php echo $name ?> .ja-box') });
</script>
<?php endif; ?>