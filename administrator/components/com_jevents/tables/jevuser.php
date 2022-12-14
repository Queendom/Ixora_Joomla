<?php
/**
 * JEvents Component for Joomla! 3.x
 *
 * @version     $Id: jevuser.php 3178 2012-01-13 09:44:58Z geraintedwards $
 * @package     JEvents
 * @copyright   Copyright (C)  2008-2022 GWESystems Ltd
 * @license     GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
 * @link        http://www.jevents.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * User Table class
 *
 * @subpackage    Users
 * @since         1.0
 */
class TableUser extends Table
{
	/**
	 * Primary Key
	 *
	 * @var int
	 */
	var $id = null;

	var $user_id = null;
	var $published = null;

	var $cancreate = null;
	var $canedit = null;

	var $canpublishown = null;
	var $candeleteown = null;

	var $canpublishall = null;
	var $candeleteall = null;

	var $canuploadimages = null;
	var $canuploadmovies = null;

	// extras
	var $cancreateown = null;
	var $cancreateglobal = null;
	var $eventslimit = null;
	var $extraslimit = null;

	// permissions
	var $categories = "";
	var $calendars = "";
	//var $inheritcats = 0;
	//var $inheritcals = 0;

	// common limit for all extras e.g. artists or locations

	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 *
	 * @since 1.0
	 */
	function __construct()
	{

		$db = Factory::getDbo();
		parent::__construct('#__jev_users', 'id', $db);
	}

	public static function checkTable()
	{

		$db = Factory::getDbo();
	}

	public static function getUsers($ids = array())
	{

		$app    = Factory::getApplication();
		$input  = $app->input;

		$where = array();
		$join  = array();
		if (is_array($ids))
		{
			if (count($ids) > 0)
			{
				$ids      = ArrayHelper::toInteger($ids);
				$idstring = implode(",", $ids);
				$where[]  = " tl.id in ($idstring)";
			}
		}
		else
		{
			$idstring = intval($ids);
			$where[]  = "tl.id in ($idstring)";
		}

		$db     = Factory::getDbo();
		$search = $app->getUserStateFromRequest("usersearch{" . JEV_COM_COMPONENT . "}", 'search', '');
		$search = $db->escape(trim(strtolower($search)));
		if ($search != "")
		{
			$where[] = " ( ju.name like '$search%' OR ju.username like '$search%')";
		}

		PluginHelper::importPlugin("jevents");
		$set        = $app->triggerEvent('getAuthorisedUser', array(& $where, & $join));

		$orderdir = $input->getCmd("filter_order_Dir", 'asc');
		$order    = $input->getCmd("filter_order", 'tl.id');

		$dir      = $orderdir == "asc" ? "asc" : "desc";
		$order    = " ORDER BY " . $order . " " . $orderdir;

		$sql = "SELECT tl.*, ju.name as jname, ju.username  FROM #__jev_users AS tl ";
		$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
		$sql .= count($join) > 0 ? implode(" ", $join) : "";
		$sql .= count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";
		$sql .= $order;

		$db->setQuery($sql);

		try {
			$users = $db->loadObjectList('id');
		} catch (Exception $e) {
			echo $e;
		}

		$total = count($users);

		$option     = JEV_COM_COMPONENT;
		$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = $app->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');

		if ($limit > 0 || $limitstart > 0)
		{
			if ($limitstart > $total)
			{
				$limitstart = 0;
			}

			$sql = "SELECT tl.*, ju.name as jname, ju.username  FROM #__jev_users AS tl ";
			$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
			$sql .= count($join) > 0 ? implode(" ", $join) : "";
			$sql .= count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";
			$sql .= $order;
			$sql .= " LIMIT $limitstart, $limit";

			$db->setQuery($sql);
			$users = $db->loadObjectList('id');
		}

		foreach ($users as $key => $val)
		{
			$user = new TableUser();
			$user->bind(get_object_vars($val));
			$user->jname    = $val->jname;
			$user->username = $val->username;
			$users[$key]    = $user;
		}

		return $users;
	}

	function bind($array, $ignore = '')
	{

		$success = parent::bind($array, $ignore);

		if (key_exists('categories', $array))
		{
			if ($array['categories'] == 'all' || $array['categories'] == 'none') $this->categories = $array['categories'];
			else if (is_array($array['categories']))
			{
				$array['categories'] = ArrayHelper::toInteger($array['categories']);
				$this->categories    = implode("|", $array['categories']);
			}
		}
		if (key_exists('calendars', $array))
		{
			if ($array['calendars'] == 'all' || $array['calendars'] == 'none') $this->calendars = $array['calendars'];
			else if (is_array($array['calendars']))
			{
				$array['calendars'] = ArrayHelper::toInteger($array['calendars']);
				$this->calendars    = implode("|", $array['calendars']);
			}
		}

		return $success;
	}

	public static function getUserCount()
	{

		PluginHelper::importPlugin("jevents");
		$where      = array();
		$join       = array();
		$set        = Factory::getApplication()->triggerEvent('getAuthorisedUser', array(& $where, & $join));

		$db  = Factory::getDbo();
		$sql = "SELECT tl.*, ju.name as jname, ju.username  FROM #__jev_users AS tl ";
		$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
		$sql .= count($join) > 0 ? implode(" ", $join) : "";
		$sql .= count($where) > 0 ? " WHERE " . implode(" AND ", $where) : "";

		$db->setQuery($sql);

		$users  = 0;

		try
		{
			$users = $db->loadObjectList('id');

		} catch (Exception $e) {
			echo $e;
		}

		return count($users);

	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 * @since  1.0
	 */
	function check()
	{

		return true;
	}

	function authorisedUser($lang = 0)
	{

		$user  = Factory::getUser();
		$users = TableUser::getUsersByUserid($user->id, "langid");
		if (count($users) > 0 && $lang <= 0) return true;
		if (array_key_exists($lang, $users)) return $users[$lang];
		if (count($users) > 0)
		{
			foreach ($users as $user)
			{

				if ($user->langid == $lang && $user->published)
				{
					return true;
				}
			}
		}

		return false;
	}

	public static function getUsersByUserid($userid, $index = "id")
	{

		if (is_array($userid))
		{
			$userid  = ArrayHelper::toInteger($userid);
			$userids = implode(",", $userid);
		}
		else
		{
			$userids = intval($userid);
		}

		PluginHelper::importPlugin("jevents");
		$where      = array();
		$join       = array();
		$set        = Factory::getApplication()->triggerEvent('getAuthorisedUser', array(& $where, & $join));

		$db  = Factory::getDbo();
		$sql = "SELECT tl.*, ju.name as jname, ju.username  FROM #__jev_users AS tl ";
		$sql .= " LEFT JOIN #__users as ju ON tl.user_id=ju.id ";
		$sql .= count($join) > 0 ? implode(" ", $join) : "";
		$sql .= " WHERE ju.id IN ( " . $userids . " )";
		$sql .= count($where) > 0 ? " AND " . implode(" AND ", $where) : "";

		$db->setQuery($sql);
		$users = 0;

		try
		{
			$users = $db->loadObjectList($index);

		} catch (Exception $e) {
			echo $e;
		}

		foreach ($users as $key => $val)
		{
			$user = new TableUser();
			$user->bind(get_object_vars($val));
			$user->jname    = $val->jname;
			$user->username = $val->username;
			$users[$key]    = $user;
		}

		return $users;
	}

	function canpublishown()
	{

		if ($this->canpublishown)
		{
			return true;
		}

		return false;
	}

	function candeleteown()
	{

		if ($this->candeleteown)
		{
			return true;
		}

		return false;
	}

	function canpublishall()
	{

		if ($this->canpublishall)
		{
			return true;
		}

		return false;
	}

	function candeleteall()
	{

		if ($this->candeleteall)
		{
			return true;
		}

		return false;
	}

	function disableAll()
	{

		$this->cancreate = 0;
		$this->canedit   = 0;

		$this->canpublishown = 0;
		$this->candeleteown  = 0;

		$this->canpublishall = 0;
		$this->candeleteall  = 0;

		$this->canuploadimages = 0;
		$this->canuploadmovies = 0;

		// extras
		$this->cancreateown    = 0;
		$this->cancreateglobal = 0;
		$this->eventslimit     = 0;
		$this->extraslimit     = 0;
	}

}

class JEVUser extends TableUser
{
	// RSH Kludge to get the JEVuser plugin to work correctly!  J!.1.6 expects the class name to be the same as the file name!
}
