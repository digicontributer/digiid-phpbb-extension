<?php
/*
Copyright 2014 Daniel Esteban

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

    http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

namespace DigiByte\digiid\core;

use phpbb\datetime;

/**
 * Class DigiID
 */
class DigiIDCallback
{


	public function __construct($db, $user)
	{
		$this->db = $db;
		$this->user = $user;
	}

	private function digiid_get_nonce($nonce_action)
	{
		global $table_prefix;
		$table_name_nonce = $table_prefix . 'digibyte_digiid_nonce';

		$session_id = $this->user->data['session_id'];
		$date = new datetime($this->user);
		$THREE_HOURS = 10800;
		$three_hours_ago = $date->getTimestamp() - $THREE_HOURS;
		$query = "DELETE FROM " . $table_name_nonce . " WHERE birth < " . (int) $three_hours_ago;
		$result = $this->db->sql_query($query);

		$query = 'SELECT * 
				FROM ' . $table_name_nonce . "
				WHERE nonce_action = '" . $this->db->sql_escape($nonce_action) . "'
					AND session_id = '" . $this->db->sql_escape($session_id) . "'";
		$result = $this->db->sql_query($query);
		$nonce_row = $this->db->sql_fetchrow($result);

		if($nonce_row)
		{
			return $nonce_row['nonce'];
		}

		$nonce_row = array();
		$nonce_row['nonce'] = DigiID::generateNonce();
		$nonce_row['nonce_action'] = $nonce_action;
		$nonce_row['session_id'] = $session_id;
		$nonce_row['birth'] =  $date->getTimestamp();

		$user_id = $this->user->data['user_id'];
		if($user_id)
		{
			$nonce_row['user_id'] = $user_id;
		}

		$query = 'INSERT INTO ' . $table_name_nonce . ' ' . $this->db->sql_build_array('INSERT', $nonce_row);
		$db_result = $this->db->sql_query($query);

		if($db_result)
		{
			return $nonce_row['nonce'];
		}
		else
		{
			return $db_result;
		}
	}

	public function digiid_get_callback_url($nonce = NULL, $nonce_action = NULL)
	{
		if(!$nonce AND $nonce_action)
		{
			$nonce = $this->digiid_get_nonce($nonce_action);
		}

		if(!$nonce)
		{
			return FALSE;
		}

		$url = generate_board_url() . "/digiid/callback?x=" . $nonce;
		if(substr($url, 0, 8) == 'https://')
		{
			return 'digiid://' . substr($url, 8);
		}
		else
		{
			return 'digiid://' . substr($url, 7) . "&u=1";
		}
	}

	public function get_digiid_callback($method)
	{
		return $this->digiid_get_callback_url(NULL, $method);
	}
}
