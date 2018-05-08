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
class login
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
	public function handle($name = 'login')
	{

			
		$callback = new DigiIDCallback($this->db, $this->user);
		$digiid = $callback->get_digiid_callback('login');
		$this->template->assign_var('URL', $digiid);
		
		return $this->helper->render('digiid_body.html', $name);
	}
}
