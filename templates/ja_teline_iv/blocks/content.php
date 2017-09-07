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

<?php if ($this->countModules ('breadcrumbs')): ?>
<div id="ja-navhelper-top">
	<div class="ja-breadcrums">
		<a href="javascript: history.go(-1)" class="ja-back-btn" title="Go back one page!"><span>Back</span></a>
		<jdoc:include type="module" name="breadcrumbs" />
	</div>
</div>
<?php endif ?>
<jdoc:include type="component" />