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

use phpbb\db\driver\driver_interface;
use phpbb\request\request_interface;
use phpbb\datetime;
use DigiByte\digiid\core\DigiID;

/**
 * DigiID main controller.
 */
class main
{

	protected $auth;
	/**
	* @var driver_interface
	*/
	protected $db;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;


	/**
	* @var request_interface
	*/
	protected $request;

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
	public function __construct(\phpbb\auth\auth $auth, driver_interface $db, \phpbb\config\config $config, \phpbb\controller\helper $helper, request_interface $request, \phpbb\template\template $template, \phpbb\user $user)
	{
		$this->auth = $auth;
		$this->db = $db;
		$this->config = $config;
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
	public function handle($name)
	{
		if($name === 'ajax')
		{
			$class = new ajax($this->config, $this->db, $this->helper, $this->request, $this->template, $this->user);
			return $class->handle();
		}
		else if ($name === 'callback')
		{
			$class = new callback($this->config, $this->db, $this->helper, $this->request, $this->template, $this->user);
			return $class->handle();
		}
		else if ($name === 'login')
		{
			$class = new login($this->config, $this->db, $this->helper, $this->request, $this->template, $this->user);
			return $class->handle();			
		}
		else if ($name === 'add')
		{
			$class = new add($this->config, $this->db, $this->helper, $this->request, $this->template, $this->user);
			return $class->handle();
		}
	}
}
