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

/**
* Add more feature for mega menu
* equal height for columns in submenu
* correct position for array in submenu
**/

function mega_equal_cols () {
}

function mega_fix_arrow_pos () {
	//var ul = $('#ja-megamenu .level0');
	var jamenu = $('ja-megamenu');
	if (!jamenu) return;
	var ul = jamenu.getElement("ul.megamenu");
	if (!ul) return;
	var lis = ul.getChildren();
	if (!lis) return;
	lis.each (function(li){
		if (!li.hasClass ('haschild')) return;
		var submenu = li.getElement('.childcontent');
		if (!submenu) return;
		var ml = submenu.getStyle('margin-left');
		if (ml) ml = ml.toInt();
		if (!ml) return;
		var childinner = submenu.getElement('.childcontent-inner');
		if (!childinner) return;
		var curpos = 26;
		childinner.setStyle ('background-position', (curpos-ml)+'px top');
	});
}

window.addEvent('load', function (){
	mega_fix_arrow_pos.delay(10);
});
