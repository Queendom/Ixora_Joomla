<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

$selector = empty($displayData['selector']) ? '' : $displayData['selector'];
$id = empty($displayData['id']) ? '' : $displayData['id'];
$active = empty($displayData['active']) ? '' : $displayData['active'];
$title = empty($displayData['title']) ? '' : $displayData['title'];
$attrTab = \T4\Helper\T4Bootstrap::getAttrs(array('toggle'=>'tab'));
$li = '<li class="nav-item' . $active . '"><a class="nav-link' . $active . '" href="#' . $id . '" '.$attrTab.'>' . $title . '</a></li>';

echo 'jQuery(function($){ $(', json_encode('#' . $selector . 'Tabs'), ').append($(', json_encode($li), ')); });';
