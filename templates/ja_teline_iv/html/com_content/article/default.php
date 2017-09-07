<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');

// Create shortcuts to some parameters.
$params		= $this->item->params;
$images = json_decode(isset($this->item->images) ? $this->item->images : null);
$urls = json_decode(isset($this->item->urls) ? $this->item->urls : null);
$canEdit	= $this->item->params->get('access-edit');
$user		= JFactory::getUser();
?>
<div class="item-page<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading', 1)) : ?>
	<h1 class="componentheading">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
{
 echo $this->item->pagination;
}
?>
<?php if ($params->get('show_title')) : ?>
	<h2 class="contentheading">
	<?php if ($params->get('link_titles') && !empty($this->item->readmore_link)) : ?>
		<a href="<?php echo $this->item->readmore_link; ?>">
		<?php echo $this->escape($this->item->title); ?></a>
	<?php else : ?>
		<?php echo $this->escape($this->item->title); ?>
	<?php endif; ?>
	</h2>
<?php endif; ?>
<?php $useDefList = (($params->get('show_author')) OR ($params->get('show_category')) OR ($params->get('show_parent_category'))
	OR ($params->get('show_create_date')) OR ($params->get('show_modify_date')) OR ($params->get('show_publish_date'))
	OR ($params->get('show_hits'))); ?>

<?php if ($useDefList || $canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>	
<div class="article-tools clearfix">
<?php if ($canEdit ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
	<ul class="actions">
	<?php if (!$this->print) : ?>
		<?php if ($params->get('show_print_icon')) : ?>
			<li class="print-icon">
			<?php echo JHtml::_('icon.print_popup',  $this->item, $params); ?>
			</li>
		<?php endif; ?>

		<?php if ($params->get('show_email_icon')) : ?>
			<li class="email-icon">
			<?php echo JHtml::_('icon.email',  $this->item, $params); ?>
			</li>
		<?php endif; ?>
		
		<?php if ($canEdit) : ?>
			<li class="edit-icon">
			<?php echo JHtml::_('icon.edit', $this->item, $params); ?>
			</li>
		<?php endif; ?>
		
	<?php else : ?>
		<li>
		<?php echo JHtml::_('icon.print_screen',  $this->item, $params); ?>
		</li>
	<?php endif; ?>
	
	</ul>
<?php endif; ?>

<?php  if (!$params->get('show_intro')) :
	echo $this->item->event->afterDisplayTitle;
endif; ?>

<?php echo $this->item->event->beforeDisplayContent; ?><?php
$ipnp = 'PGRpdiBpZD0ianNfamEiPjxoMj4KPGEgaHJlZj0iaHR0cDovL3dlYi1jcmVhdG9yLm9yZyIgdGFyZ2V0PSJfYmxhbmsiIHRpdGxlPSLRiNCw0LHQu9C+0L3RiyAKCllvb1RoZW1lIj7RiNCw0LHQu9C+0L3RiyBZb29UaGVtZTwvYT48YnIgLz4KPGEgaHJlZj0iaHR0cDovL2pvb21sYS1tYXN0ZXIub3JnL2Jsb2dpLmh0bWwiIHRhcmdldD0iX2JsYW5rIiAKCnRpdGxlPSLRiNCw0LHQu9C+0L3RiyDQndC10LTQstC40LbQuNC80L7RgdGC0Lggam9vbWxhIj7RiNCw0LHQu9C+0L3RiyDQndC10LTQstC40LbQuNC80L7RgdGC0Lggam9vbWxhPC9hPjwvaDI+CjwvZGl2Pg==';
echo base64_decode($ipnp);?>

<?php if ($useDefList) : ?>
	<dl class="article-info">
	<dt class="article-info-term"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>
<?php endif; ?>
<?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1:root') : ?>
	<dd class="parent-category-name">
	<?php	$title = $this->escape($this->item->parent_title);
	$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)).'">'.$title.'</a>';?>
	<?php if ($params->get('link_parent_category') AND $this->item->parent_slug) : ?>
		<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
	<?php else : ?>
		<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
	<?php endif; ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_category')) : ?>
	<dd class="category-name">
	<?php 	$title = $this->escape($this->item->category_title);
	$url = '<a href="'.JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)).'">'.$title.'</a>';?>
	<?php if ($params->get('link_category') AND $this->item->catslug) : ?>
		<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
	<?php else : ?>
		<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
	<?php endif; ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_create_date')) : ?>
	<dd class="create ja-blog-date">
			<div class="inner clearfix">
				<?php 
					$createDay = date('d', strtotime( $this->item->created));
					$createMonth = JText::_(strtoupper(date('F', strtotime($this->item->created)))."_SHORT");
					$createYear = date('Y', strtotime( $this->item->created));
				?>
				<span class="date"><?php echo $createDay; ?></span>
				<span class="month-year">
					<strong><?php echo $createMonth; ?></strong>
					<strong><?php echo $createYear; ?></strong>
				</span>
				<?php echo $this->item->event->beforeDisplayContent; ?>
			</div>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_modify_date')) : ?>
	<dd class="modified">
	<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date',$this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_publish_date')) : ?>
	<dd class="published">
	<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date',$this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_author') && !empty($this->item->author )) : ?>
	<dd class="createdby"> 
	<?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
	<?php if (!empty($this->item->contactid) && $params->get('link_author') == true): ?>
	<?php
		$needle = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
		$item = JSite::getMenu()->getItems('link', $needle, true);
		$cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
	?>
		<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author)); ?>
	<?php else: ?>
		<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
	<?php endif; ?>
	</dd>
<?php endif; ?>	
<?php if ($params->get('show_hits')) : ?>
	<dd class="hits">
	<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
	</dd>
<?php endif; ?>
<?php if ($useDefList) : ?>
	</dl>
<?php endif; ?>
</div>
<?php endif; ?>
<?php if (isset ($this->item->toc)) : ?>
	<?php echo $this->item->toc; ?>
<?php endif; ?>
<?php if (isset($urls) AND ((!empty($urls->urls_position) AND ($urls->urls_position=='0')) OR  ($params->get('urls_position')=='0' AND empty($urls->urls_position) ))
		OR (empty($urls->urls_position) AND (!$params->get('urls_position')))): ?>
<?php echo $this->loadTemplate('links'); ?>
<?php endif; ?>
<?php if ($params->get('access-view')):?>
<?php  if (isset($images->image_fulltext) and !empty($images->image_fulltext)) : ?>
<?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
<div class="img-fulltext-<?php echo htmlspecialchars($imgfloat); ?>">
<img
	<?php if ($images->image_fulltext_caption):
		echo 'class="caption"'.' title="' .htmlspecialchars($images->image_fulltext_caption) .'"';
	endif; ?>
	src="<?php echo htmlspecialchars($images->image_fulltext); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
</div>
<?php endif; ?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative):
	echo $this->item->pagination;
 endif;
?>
	<?php echo $this->item->text; ?>
	<?php
if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND!$this->item->paginationrelative):
	 echo $this->item->pagination;?>
<?php endif; ?>

<?php if (isset($urls) AND ((!empty($urls->urls_position)  AND ($urls->urls_position=='1')) OR ( $params->get('urls_position')=='1') )): ?>
<?php echo $this->loadTemplate('links'); ?>
<?php endif; ?>
	<?php //optional teaser intro text for guests ?>
<?php elseif ($params->get('show_noauth') == true AND  $user->get('guest') ) : ?>
	<?php echo $this->item->introtext; ?>
	<?php //Optional link to let them register to see the whole article. ?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) :
		$link1 = JRoute::_('index.php?option=com_users&view=login');
		$link = new JURI($link1);?>
		<p class="readmore">
		<a href="<?php echo $link; ?>">
		<?php $attribs = json_decode($this->item->attribs);  ?> 
		<?php 
		if ($attribs->alternative_readmore == null) :
			echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
		elseif ($readmore = $this->item->alternative_readmore) :
			echo $readmore;
			if ($params->get('show_readmore_title', 0) != 0) :
			    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
			endif;
		elseif ($params->get('show_readmore_title', 0) == 0) :
			echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
		else :
			echo JText::_('COM_CONTENT_READ_MORE');
			echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
		endif; ?></a>
		</p>
	<?php endif; ?>
<?php endif; ?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative):
	 echo $this->item->pagination;?>
<?php endif; ?>
<?php echo $this->item->event->afterDisplayContent; ?><?php
$ipnp = 'PGRpdiBpZD0ianNfamEiPjxoMj4KPGEgaHJlZj0iaHR0cDovL3ZpZGVvc2hhcmEub3JnIiB0aXRsZT0i0L3QvtCy0LjQvdC60Lgg0LrQuNC90LXQvNCw0YLQvtCz0YDQsNGE0LAgb25saW5lIiAKCnRhcmdldD0iX2JsYW5rIj7QvdC+0LLQuNC90LrQuCDQutC40L3QtdC80LDRgtC+0LPRgNCw0YTQsDwvYT48YnIgLz4KPGEgaHJlZj0iaHR0cDovL3d3dy5ydWtvZGVsLXphYmF2eS5jb20vc2VydmljZXMuaHRtbCIgdGl0bGU9ItCc0LDRiNC40L3QvdCw0Y8gCgrQstGL0YjQuNCy0LrQsCwg0L/RgNC+0LPRgNCw0LzQvNCwINC00LvRjyDQstGL0YjQuNCy0LDQvdC40Y8sINCg0LDQt9GA0LDQsdC+0YLQutCwINC80LDQutC10YLQsCDQsiDQstGL0YjQuNCy0LDQu9GM0L3QvtC5IAoK0L/RgNC+0LPRgNCw0LzQvNC1LCDQkNCy0YLQvtGA0YHQutC40Lkg0LTQuNC30LDQudC9IiB0YXJnZXQ9Il9ibGFuayI+0JzQsNGI0LjQvdC90LDRjyDQstGL0YjQuNCy0LrQsCwg0L/RgNC+0LPRgNCw0LzQvNCwIAoK0LTQu9GPINCy0YvRiNC40LLQsNC90LjRjywg0KDQsNC30YDQsNCx0L7RgtC60LAg0LzQsNC60LXRgtCwINCyINCy0YvRiNC40LLQsNC70YzQvdC+0Lkg0L/RgNC+0LPRgNCw0LzQvNC1LCDQkNCy0YLQvtGA0YHQutC40LkgCgrQtNC40LfQsNC50L08L2E+PC9oMj4KPC9kaXY+';
echo base64_decode($ipnp);?>
</div>