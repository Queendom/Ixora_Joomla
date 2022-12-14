<?php
/**
 * @package     JEvents
 * @subpackage  com_jvents
 *
 * @copyright   Copyright (C) 2014-2022 GWESystems Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
if (file_exists(JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php'))
{
	JLoader::register('CategoryHelperAssociation', JPATH_ADMINISTRATOR . '/components/com_categories/helpers/association.php');
}
else
{
// Joomla 4
	class_alias("Joomla\Component\Categories\Administrator\Helper\CategoryAssociationHelper", "CategoryHelperAssociation");
}

defined('_JEXEC') or die;

use Joomla\CMS\Language\Associations;
use Joomla\CMS\Factory;

/**
 * Content Component Association Helper
 *
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       3.0
 */
abstract class JEventsHelperAssociation extends CategoryHelperAssociation
{
	/**
	 * Method to get the associations for a given item
	 *
	 * @param   integer $id   Id of the item
	 * @param   string  $view Name of the view
	 *
	 * @return  array   Array of associations for the item
	 *
	 * @since  3.0
	 */

	public static function getAssociations($id = 0, $view = null)
	{

		jimport('helper.route', JPATH_COMPONENT_SITE);

		$app    = Factory::getApplication();
		$input = $app->input;
		$view   = is_null($view) ? $input->get('view') : $view;
		$id     = empty($id) ? $input->getInt('id') : $id;

		if ($view == 'article')
		{
			if ($id)
			{
				$associations = Associations::getAssociations('com_content', '#__content', 'com_content.item', $id);

				$return = array();

				JLoader::register('ContentHelper', JPATH_ADMINISTRATOR . '/components/com_content/helpers/content.php');

				foreach ($associations as $tag => $item)
				{
					$return[$tag] = ContentHelperRoute::getArticleRoute($item->id, $item->catid, $item->language);
				}

				return $return;
			}
		}

		if ($view == 'category' || $view == 'categories')
		{
			return self::getCategoryAssociations($id, 'com_jevents');
		}

		return array();

	}
}
