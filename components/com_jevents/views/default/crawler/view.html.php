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

use Joomla\CMS\Component\ComponentHelper;

/**
 * HTML View class for the component frontend
 *
 * @static
 */
class DefaultViewCrawler extends JEventsDefaultView
{

	function listevents($tpl = null)
	{

		JEVHelper::componentStylesheet($this);

		$params = ComponentHelper::getParams(JEV_COM_COMPONENT);

		$value = trim($params->get("relstart", ""));
		if ($value != "")
		{
			$value     = str_replace(",", " ", $value);
			$value     = str_replace("y", "year", $value);
			$value     = str_replace("d", "day", $value);
			$value     = str_replace("w", "week", $value);
			$value     = str_replace("m", "month", $value);
			$value     = new JevDate($value);
			$startdate = $value->toFormat("%Y-%m-%d");
			list($startyear, $startmonth, $startday) = explode("-", $startdate);
		}
		else
		{
			$startyear  = JEVHelper::getMinYear();
			$startdate  = $startyear . "-01-01";
			$startmonth = 1;
			$startday   = 1;
		}
		if ($value != "")
		{
			$value   = trim($params->get("relend", ""));
			$value   = str_replace(",", " ", $value);
			$value   = str_replace("y", "year", $value);
			$value   = str_replace("d", "day", $value);
			$value   = str_replace("w", "week", $value);
			$value   = str_replace("m", "month", $value);
			$value   = new JevDate($value);
			$enddate = $value->toFormat("%Y-%m-%d");
			list($endyear, $endmonth, $endday) = explode("-", $enddate);
		}
		else
		{
			$endyear  = JEVHelper::getMaxYear();
			$enddate  = $endyear . "-12-31";
			$endmonth = 12;
			$endday   = 31;
		}

		$this->startdate    = $startdate;
		$this->startyear    = $startyear;
		$this->startmonth   = $startmonth;
		$this->startday     = $startday;
		$this->enddate      = $enddate;
		$this->endyear      = $endyear;
		$this->endmonth     = $endmonth;
		$this->endday       = $endday;

		// Note that using a $limit value of -1 the limit is ignored in the query
		$this->data = $this->datamodel->getRangeData($startdate, $enddate, $this->limit, $this->limitstart);

	}
}
