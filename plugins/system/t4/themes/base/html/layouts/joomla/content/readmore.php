<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


defined('JPATH_BASE') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

$params    = $displayData['params'];
$item      = $displayData['item'];
$direction = Factory::getLanguage()->isRtl() ? 'left' : 'right';
$readmoreText = version_compare(JVERSION, '4', 'ge') ? Text::_('JGLOBAL_READ_MORE') : Text::_('COM_CONTENT_READ_MORE_TITLE');
$readmoreShowTitle = version_compare(JVERSION, '4', 'ge') ? Text::sprintf('JGLOBAL_READ_MORE_TITLE', HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'))) : Text::_('COM_CONTENT_READ_MORE') ." ".HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit'));
?>

<p class="readmore">
	<?php if (!$params->get('access-view')) : ?>
		<a class="btn btn-readmore" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
			<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo Text::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
		</a>
	<?php elseif ($readmore = $item->alternative_readmore) : ?>
		<a class="btn btn-readmore" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo $readmore; ?>
			<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
				<?php echo HTMLHelper::_('string.truncate', $item->title, $params->get('readmore_limit')); ?>
			<?php endif; ?>
		</a>
	<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
		<a class="btn btn-readmore" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_READ_MORE'); ?> <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo $readmoreText; ?>
		</a>
	<?php else : ?>
		<a class="btn btn-readmore" href="<?php echo $displayData['link']; ?>" itemprop="url" aria-label="<?php echo Text::_('COM_CONTENT_READ_MORE'); ?> <?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?>">
			<?php echo $readmoreShowTitle; ?>
		</a>
	<?php endif; ?>
</p>
