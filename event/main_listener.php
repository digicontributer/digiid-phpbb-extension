<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\request\request_interface;

/**
 * DigiID Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'				=> 'load_language_on_setup',
			'core.login_box_before'	=> 'load_digiid_authentication',
		);
	}

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	/* @var \phpbb\request\request_interface */
	protected $request;

	/* @var \phpbb\template\template */
	protected $template;

	/* @var \phpbb\user */
	protected $user;

	/** @var string phpEx */
	protected $php_ext;


	/**
	 * Constructor
	 *
	 * @param \phpbb\controller\helper	$helper		Controller helper object
	 * @param \phpbb\template\template	$template	Template object
	 * @param \phpbb\user               $user       User object
	 * @param string                    $php_ext    phpEx
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, request_interface $request, \phpbb\template\template $template, \phpbb\user $user, $php_ext)
	{
		$this->config   = $config;
		$this->helper   = $helper;
		$this->request  = $request;
		$this->template = $template;
		$this->user     = $user;
		$this->php_ext  = $php_ext;
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'DigiByte/digiid',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function load_digiid_authentication($event)
	{
		$this->tpl_name   = 'digiid_body';
		$this->page_title = 'digiid_body';

		$method = request_var('method', '');
		
		if($this->user->page['page_dir'] !== 'adm' && $method !== 'old' && $this->config['DigiByte_digiid_enable'] === "1")
		{
			return header( 'Location: ' . generate_board_url() . '/digiid/login' );
		}
	}

}
