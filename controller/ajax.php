<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\controller;

$base = __DIR__ . '/../';
require_once($base  . "./core/digiid.php");
require_once($base  . "./core/digiidcallback.php");

use phpbb\db\driver\driver_interface;
use phpbb\request\request_interface;
use phpbb\datetime;
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;

/**
 * DigiID main controller.
 */
class ajax
{
	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\controller\helper	$helper
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\user				$user
	 */
	public function __construct(\phpbb\config\config $config, driver_interface $db, \phpbb\controller\helper $helper, request_interface $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
	}

	/**
	 * DigiByte controller for route /digiid/{name}
	 *
	 * @param string $name
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($name = 'ajax')
	{
		$date = new datetime($this->user);
		global $table_prefix;
		$table_name_nonce = $table_prefix . 'digibyte_digiid_nonce';
		$table_name_userlink = $table_prefix . "users";

		$session_id = $this->user->data['session_id'];
		$sql = "SELECT * FROM " . $table_name_nonce . " WHERE session_id = '" . $session_id . "'";
		$query = $this->db->sql_query($sql);
		$nonce_row = $this->db->sql_fetchrow($query);
		
		$data = array();
		
		if(!$nonce_row)
		{
			$data['status'] = -1;
			$data['html'] = "<p>Error: The current session doesn't have a digiid-nonce.</p>";
		}
		else
		{
			switch($nonce_row['nonce_action'])
			{
				case 'login':
				{
					if($nonce_row['address'])
					{
						$data['status'] = 1;
						$data['adress'] = $nonce_row['address'];
						$sql = "SELECT * FROM " . $table_name_userlink . " WHERE user_address = '" . $data['adress'] . "'";
						$query = $this->db->sql_query($sql);
						$user_row = $this->db->sql_fetchrow($query);
						$result = $this->user->session_create( $user_row['user_id'], false);
						if($user_row)
						{
							$sql = "DELETE FROM " . $table_name_nonce . " WHERE session_id = '" . $session_id . "'";
							$query = $this->db->sql_query($sql);
							
							if($this->user->data['user_id'] == ANONYMOUS	)
							{
							//	$data['html'] = "<p>" . __("Allredy logged in", 'digiid-authentication') . "</p>";
							//	$data['reload'] = 1;
							}
							else
							{
								$result = $this->user->session_create($user_row['user_id'], false);
								if($result)
								{
									$data['html'] = "<p>Success, logged in as " . $this->user->data['username'] . "</p>";
									$data['reload'] = 1;

									$sql = "UPDATE " . $table_name_userlink . " SET " .  $this->db->sql_build_array('UPDATE', array(
										'pulse' => $date->getTimestamp()
									)) . " WHERE user_address = '" . $data['adress'] . "'";
									$query = $this->db->sql_query($sql);
								}
								else
								{
									$data['stop'] = 1;
									$data['html'] = "<p>Dig-iID verification Success, but no useraccount connected to " . $data['adress'] . "</p>";
								}
							}
						}
						else
						{
							$data['stop'] = 1;
							$data['html'] = "<p>Digi-ID verification Success, but no useraccount connected to " . $data['adress'] . "</p>";
						}
					}
					else
					{
						$data['status'] = 0;
					}
	
					break;
				}

				case 'add':
					if($this->user->data['user_address']) {
						$data['status'] = 1;
						$data['reload'] = 1;
						$data['html'] = "<p>Digi-ID link success</p>";
					} else {
						$data['status'] = 0;
						$data['html'] = "<p>Digi-ID not yet linked</p>";						
					}
					break;
	
				default:
				{
					$data['status'] = -1;
					$data['html'] = "<p>Unknown action: </p>";
					break;
				}
			}
		}
	
		echo json_encode($data) . PHP_EOL;
		die();
	}
}
