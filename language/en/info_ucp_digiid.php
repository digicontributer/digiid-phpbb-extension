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
	'UCP_DIGIID'				=> 'Settings',
	'UCP_DIGIID_TITLE'		=> 'My Digi-ID',
	'UCP_DIGIID_USER'			=> 'DigiByte Address',
	'UCP_DIGIID_USER_EXPLAIN'	=> 'The DigiByte address that you will use to login',
	'UCP_DIGIID_SAVED'		=> 'Settings have been saved successfully!',
	'UCP_DIGIID_INVALID_ADDRESS'	=> ' is NOT a valid DigiByte address'
));
