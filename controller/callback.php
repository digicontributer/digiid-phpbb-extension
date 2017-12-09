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
class callback
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
	public function handle($name = 'callback')
	{
		global $table_prefix;
		$table_name_nonce = $table_prefix . 'digibyte_digiid_nonce';
		$table_name_userlink = $table_prefix . 'users';
		$raw_post_data = file_get_contents('php://input');
		
		$variables = array('address', 'signature', 'uri');
		$callback = new DigiIDCallback($this->db, $this->user);
	
		$post_data = array();
	
		$json = NULL;
		$uri = NULL;
		$nonce = NULL;
	
		$GLOBALS['digiid_vars']['json'] = &$json;
		$GLOBALS['digiid_vars']['uri'] = &$uri;
		$GLOBALS['digiid_vars']['nonce'] = &$nonce;

		if(substr($raw_post_data, 0, 1) == "{")
		{
			$json = json_decode($raw_post_data, TRUE);
			foreach($variables as $key)
			{
				if(isset($json[$key]))
				{
					$post_data[$key] = (string) $json[$key];
				}
				else
				{
					$post_data[$key] = NULL;
				}
			}
		}
		else
		{
			$json = FALSE;
			foreach($variables as $key)
			{
				if($this->request->variable($key, '') !== '')
				{
					$post_data[$key] = (string) html_entity_decode($this->request->variable($key, ''));
				}
				else
				{
					$post_data[$key] = NULL;
				}
			}
		}

		if(!array_filter($post_data))
		{
			DigiID::http_error(20, 'No data recived');
			die();
		}

		$nonce = DigiID::extractNonce($post_data['uri']);

		if(!$nonce OR strlen($nonce) != 32)
		{
			DigiID::http_error(40, 'Bad nonce');
			die();
		}

		$uri = $callback->digiid_get_callback_url($nonce);

		if($uri != $post_data['uri'])
		{
			DigiID::http_error(10, 'Bad URI', NULL, NULL, array('expected' => $uri, 'sent_uri' => $post_data['uri']));
			die();
		}


		$sql = "SELECT * FROM " . $table_name_nonce . " WHERE nonce = '" . $nonce . "'";
		$query = $this->db->sql_query($sql);
		$nonce_row = $this->db->sql_fetchrow($query);
	
		if(!$nonce_row)
		{
			DigiID::http_error(41, 'Bad or expired nonce');
			die();
		}
	
		if($nonce_row AND $nonce_row['address'] AND $nonce_row['address'] != $post_data['address'])
		{
			DigiID::http_error(41, 'Bad or expired nonce');
			die();
		}
	
		$digiid = new DigiID();
	
		$signValid = $digiid->isMessageSignatureValidSafe($post_data['address'], $post_data['signature'], $post_data['uri'], FALSE);
	
		if(!$signValid)
		{
			DigiID::http_error(30, 'Bad signature', $post_data['address'], $post_data['signature'], $post_data['uri']);
			die();
		}

		if(!$nonce_row['address'])
		{
			$nonce_row['address'] = $post_data['address'];
	
			switch($nonce_row['nonce_action'])
			{
				case 'login':
				{
					$sql = "UPDATE " . $table_name_nonce . " SET " . $this->db->sql_build_array('UPDATE', array(
						'address' => $post_data['address'],
					)) . " WHERE nonce = '" . $nonce . "'";
					$db_result = $this->db->sql_query($sql);

					if(!$db_result)
					{
						DigiID::http_error(50, 'Database failer', 500, 'Internal Server Error');
						die();
					}
					// rest is done in ajax
					break;
				}
	
				case 'add':
				{
					if($nonce_row['user_id'])
					{
						$date = new datetime($this->user);
						$sql = "UPDATE " . $table_name_userlink . " SET " . $this->db->sql_build_array('UPDATE', array("user_address" => $post_data['address'], "birth" => $date->getTimestamp())) . " WHERE user_id = " . (int) $nonce_row['user_id'];
						//$sql = "UPDATE " . $table_name_userlink . " SET user_id = '" . $nonce_row['user_id'] . "', user_address = '" . $post_data['address'] . "', birth = " . $date->getTimestamp();
						$db_result = $this->db->sql_query($sql);
						break;
					}
					else
					{
						DigiID::http_error(51, "Can't add digiid to a userless session", 500, 'Internal Server Error');
						die();
					}
				}
			}
		}
	
		DigiID::http_ok($post_data['address'], $nonce);
		die();
	}
}
