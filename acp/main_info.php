<?php
/**
 *
 * asdasd. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, asdasd, asdasd
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\acp;

/**
 * asdasd ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\DigiByte\digiid\acp\main_module',
			'title'		=> 'ACP_DIGIID_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_DIGIID',
					'auth'	=> 'ext_DigiByte/digiid && acl_a_board',
					'cat'	=> array('ACP_DIGIID_TITLE')
				),
			),
		);
	}
}
