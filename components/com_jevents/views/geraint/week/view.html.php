<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: view.html.php 3155 2012-01-05 12:01:16Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2022 GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
/**
 * HTML View class for the component frontend
 *
 * @static
 */
class GeraintViewWeek extends JEventsGeraintView
{
	function listevents($tpl = null)
	{

		JEVHelper::componentStylesheet($this);

		$document = Factory::getDocument();
		// TODO do this properly
		//$document->setTitle(Text::_( 'BROWSER_TITLE' ));

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);
		//$this->assign("introduction", $params->get("intro",""));


	}
}
