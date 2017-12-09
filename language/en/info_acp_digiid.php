<?php
/**
 *
 * asdasd. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, asdasd, asdasd
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_DIGIID_TITLE'			=> 'DigiID Module',
	'ACP_DIGIID_ENABLE'			=> 'Enable DigiID?',
));
