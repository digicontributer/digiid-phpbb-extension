<?php
/**
 *
 * asdasd. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, asdasd, asdasd
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\migrations;

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['digibyte_digiid_enabled']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('digibyte_digiid_enabled', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_DIGIID_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_DIGIID_TITLE',
				array(
					'module_basename'	=> '\DigiByte\digiid\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
