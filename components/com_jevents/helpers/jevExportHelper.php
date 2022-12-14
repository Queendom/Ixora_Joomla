<?php
/**
 * JEvents Component for Joomla
 *
 * @version     $Id: jevExportHelper.php
 * @package     JEvents
 * @copyright   Copyright (C) 2008-2022 GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\String\StringHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * JEvents component helper.
 *
 * @package        Jevents
 * @since          1.6
 */
class JevExportHelper
{

	static function getAddToGCal($row)
	{

		$eventData = JevExportHelper::getEventStringArray($row);

		$urlString['title']       = "text=" . $eventData['title'];
		$urlString['dates']       = "dates=" . $eventData['dates'];
		$urlString['location']    = "location=" . $eventData['location'];
		$urlString['trp']         = "trp=false";
		$urlString['websiteName'] = "sprop=" . $eventData['sitename'];
		$urlString['websiteURL']  = "sprop=name:" . $eventData['siteurl'];
		$urlString['details']     = "details=" . $eventData['details'];
		$link                     = "http://www.google.com/calendar/event?action=TEMPLATE&" . implode("&", $urlString);

		return $link;
	}

	static function getEventStringArray($row)
	{

		$urlString['title'] = urlencode($row->title());
		$params             = ComponentHelper::getParams(JEV_COM_COMPONENT);
		$tz                 = $params->get("icaltimezonelive", "");
		if ($tz)
		{
			$urlString['dates'] = JevDate::strftime("%Y%m%dT%H%M%S", $row->getUnixStartTime()) . "/" . JevDate::strftime("%Y%m%dT%H%M%S", $row->getUnixEndTime()) . "&ctz=" . $tz;
		}
		else
		{
			$urlString['dates'] = JevDate::strftime("%Y%m%dT%H%M%SZ", $row->getUnixStartTime()) . "/" . JevDate::strftime("%Y%m%dT%H%M%SZ", $row->getUnixEndTime());
		}
		$urlString['st']         = JevDate::strftime("%Y%m%dT%H%M%SZ", $row->getUnixStartTime());
		$urlString['et']         = JevDate::strftime("%Y%m%dT%H%M%SZ", $row->getUnixEndTime());
		$urlString['duration']   = (int) $row->getUnixEndTime() - (int) $row->getUnixStartTime();
		$urlString['duration']   = (int) $row->getUnixEndTime() - (int) $row->getUnixStartTime();
		$urlString['location']   = urlencode(isset($row->_locationaddress) ? $row->_locationaddress : $row->location());
		$urlString['sitename']   = urlencode(Factory::getApplication()->get('sitename'));
		$urlString['siteurl']    = urlencode(Uri::root());
		$urlString['rawdetails'] = urlencode($row->get('description'));
		$urlString['details']    = strip_tags($row->get('description'));
		if (StringHelper::strlen($urlString['details']) > 500)
		{
			$urlString['details'] = StringHelper::substr($urlString['details'], 0, 500) . ' ...';

			//Check if we should include the link to the event
			if ($params->get('source_url', 0) == 1)
			{
				$link                 = $row->viewDetailLink($row->yup(), $row->mup(), $row->dup(), true, $params->get('default_itemid', 0));
				$uri                  = Uri::getInstance(Uri::base());
				$root                 = $uri->toString(array('scheme', 'host', 'port'));
				$urlString['details'] .= ' ' . Text::_('JEV_EVENT_IMPORTED_FROM') . $root . Route::_($link, true, -1);
			}
		}
		$urlString['details'] = urlencode($urlString['details']);

		return $urlString;
	}

	static function getAddToYahooCal($row)
	{

		$eventData = JevExportHelper::getEventStringArray($row);

		$urlString['title']      = "title=" . $eventData['title'];
		$urlString['st']         = "st=" . $eventData['st'];
		$urlString['et']         = "et=" . $eventData['et'];
		$urlString['rawdetails'] = "desc=" . $eventData['details'];
		$urlString['location']   = "in_loc=" . $eventData['location'];
		$link                    = "http://calendar.yahoo.com/?v=60&view=d&type=20&" . implode("&", $urlString);

		return $link;
	}
}
