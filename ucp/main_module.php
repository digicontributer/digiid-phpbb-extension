<?php
/**
 *
 * DigiID. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Esotericizm, https://digibyte.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace DigiByte\digiid\ucp;

$base = __DIR__ . '/../';

require_once($base  . "./core/digiid.php");
use DigiByte\digiid\core\DigiID;
use DigiByte\digiid\core\DigiIDCallback;

/**
 * DigiID UCP module.
 */
class main_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $request, $template, $user;

		$this->tpl_name = 'ucp_digiid_body';
		$this->page_title = $user->lang('UCP_DIGIID_TITLE');
		$address = $request->variable('user_address', $user->data['user_address']);
		add_form_key('digibyte/digiid');

		$data = array(
			'user_address' => $address,
		);

		$callback = new DigiIDCallback($db, $user);
		$digiid = $callback->get_digiid_callback('add');

		if (!$address) {
			$template->assign_var('URL', $digiid);
		} else {
			$template->assign_var('URL', false);
		}

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('digibyte/digiid'))
			{
				trigger_error($user->lang('FORM_INVALID'));
			}
			$digiid = new DigiID();

			if($digiid->isAddressValid($address, FALSE))
			{
				$sql = 'UPDATE ' . USERS_TABLE . '
					SET ' . $db->sql_build_array('UPDATE', $data) . '
					WHERE user_id = ' . $user->data['user_id'];
				$db->sql_query($sql);

				meta_refresh(3, $this->u_action);
				$message = $user->lang('UCP_DIGIID_SAVED') . '<br /><br />' . $user->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);
			}
			else 
			{
				meta_refresh(3, $this->u_action);
				$message = '<b>' . $address .  '</b>' . $user->lang('UCP_DIGIID_INVALID_ADDRESS') . '<br /><br />' . $user->lang('RETURN_UCP', '<a href="' . $this->u_action . '">', '</a>');
				trigger_error($message);				
			}
		}

		$template->assign_vars(array(
			'S_USER_DIGIBYTE'	=> $data['user_address'],
			'S_UCP_ACTION'	=> $this->u_action,
		));
	}
}
