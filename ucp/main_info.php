<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\ucp;

/**
 * DigiID UCP module info.
 */
class main_info
{
	function module()
	{
		return array(
			'filename'	=> '\DigiByte\digiid\ucp\main_module',
			'title'		=> 'UCP_DIGIID_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'UCP_DIGIID',
					'auth'	=> 'ext_DigiByte/digiid',
					'cat'	=> array('UCP_DIGIID_TITLE')
				),
			),
		);
	}
}
