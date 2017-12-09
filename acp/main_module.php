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
 * asdasd ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $config, $request, $template, $user;

		$user->add_lang_ext('DigiByte/digiid', 'common');
		$this->tpl_name = 'acp_digiid_body';
		$this->page_title = $user->lang('ACP_DIGIID_TITLE');
		add_form_key('DigiByte/digiid');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('DigiByte/digiid'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}

			$config->set('DigiByte_digiid_enable', $request->variable('DigiByte_digiid_enable', 0));

			trigger_error($user->lang('ACP_DIGIID_SETTING_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'ACME_DEMO_GOODBYE'		=> $config['acme_demo_goodbye'],
			'DIGIBYTE_DIGIID_ENABLE'=> $config['DigiByte_digiid_enable'],
		));
	}
}
