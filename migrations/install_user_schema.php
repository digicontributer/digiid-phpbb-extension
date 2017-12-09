<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\migrations;

class install_user_schema extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'users', 'user_address');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_schema()
	{
		return array(
			'add_tables'		=> array(
				$this->table_prefix . 'digibyte_digiid_nonce'	=> array(
					'COLUMNS'		=> array(
						'nonce'					=> array('VCHAR', ''),
						'address'				=> array('VCHAR', ''),
						'user_id' 				=> array('BINT', 0),
						'nonce_action' 			=> array('VCHAR', ''),
						'birth'					=> array('TIMESTAMP', 0),
						'session_id'			=> array('VCHAR', '')
					),
					'PRIMARY_KEY'	=> 'nonce'
				)

			),
			'add_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'user_address'						=> array('VCHAR', ''),
					'user_nonce'						=> array('VCHAR', ''),
					'user_nonce_action' 				=> array('VCHAR', NULL),
					'birth'								=> array('TIMESTAMP', 0),
					'pulse'								=> array('TIMESTAMP', 0)
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'user_address',
					'user_nonce',
					'user_nonce_action',
					'birth',
					'pulse'
				),
			),
			'drop_tables'		=> array(
				$this->table_prefix . 'digibyte_digiid_nonce',
			),
		);
	}
}
