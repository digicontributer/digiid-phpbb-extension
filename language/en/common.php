<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
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
	'DIGIID_PAGE'			=> 'DigiByte',

	'ACP_DIGIID'					=> 'Settings',
	'ACP_DIGIID_SETTING_SAVED'	=> 'Settings have been saved successfully!'
));
