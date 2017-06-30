<?php

/* Q2A Email Notifications
 * Copyright (C) 2011-13  Walter Williams
 *                        Foivos S. Zakkak
 *
 * https://github.com/sawtoothsoftware/Q2A-Email-Notifications
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


require_once QA_INCLUDE_DIR.'qa-app-users.php';
require_once QA_INCLUDE_DIR.'qa-db-maxima.php';
require_once QA_INCLUDE_DIR.'qa-util-string.php';
require_once QA_INCLUDE_DIR.'qa-db-selects.php';
require_once QA_INCLUDE_DIR.'qa-app-captcha.php';


class qa_user_email_notifications_page
{
	var $captchaerrors;

	function load_module ($directory, $urltoroot)
	{
	}

	function suggest_requests () // for display in admin interface
	{
		return array(
			array(
				'title' => 'User Email Notifications',
				'request' => 'qa-user-email-notifications-page',
				'nav' => 'F', // 'M'=main, 'F'=footer, 'B'=before main, 'O'=opposite main, null=none
			),
		);
	}

	function match_request ($request)
	{
		return ($request=='qa-user-email-notifications-page');
	}

	function process_request ($request)
	{
		$qa_content=qa_content_prepare();

		$qa_content['title']='Email Notifications';

		$subresult = false;
		$subresultmsg = '';
		if (qa_post_text('optin') == '0')
		{
			qa_captcha_validate_post($captchaerrors);
			if (empty($captchaerrors))
			{
				if (qa_post_text('email'))
					$subresult = $this->subscribe(qa_post_text('email'), $subresultmsg, qa_post_text('favonly'));
			}
		}
		else if (qa_post_text('optin') == '1')
		{
			qa_captcha_validate_post($captchaerrors);
			if (empty($captchaerrors))
			{
				if (qa_post_text('email'))
					$subresult = $this->unsubscribe($subresultmsg);
			}
		}

		$qa_content['form']=array(
			'tags' => 'METHOD="POST" ACTION="'.qa_self_html().'"',

			'style' => 'wide',

			'ok' => (empty($captchaerrors) && $subresult) ? $subresultmsg : null,

			'title' => 'To subscribe or unsubscribe to receive emails when a new question is posted, please enter your email address:',

			'fields' => array(
				'suboptin' => array(
					'label' => '',
					'tags' => 'NAME="optin"',
					'type' => 'select-radio',
					'options' => array('Subscribe', 'Unsubscribe'),
					'value' => 'Subscribe',
					'error' => '',
				),
				'favolnly' => array(
					'label' => 'Receive notifications only for<br/> favorite questions and categories',
					'tags' => 'NAME="favonly"',
					'type' => 'checkbox',
					'value' => 1,
					'error' => '',
				),
				'request' => array(
					'label' => 'Email address',
					'tags' => 'NAME="email"',
					'value' => qa_get_logged_in_email(),
					'error' => (empty($captchaerrors) && !$subresult) ? qa_html($subresultmsg) : '',
				),
			),

			'buttons' => array(
				'ok' => array(
					'tags' => 'NAME="ok"',
					'label' => 'OK',
					'value' => '1',
				),
			),
		);
		qa_set_up_captcha_field($qa_content, $qa_content['form']['fields'], @$captchaerrors);

		return $qa_content;
	}

	function subscribe ($email, &$message, $favonly)
	{
		if ($this->verify_email($email))
		{
			qa_db_query_sub("CREATE TABLE IF NOT EXISTS ^useremailsubscription (userid int(10) unsigned NOT NULL, email varchar(80) NOT NULL, registered timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, favoritesonly bit(1) NOT NULL DEFAULT b'1', PRIMARY KEY (userid)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			qa_db_query_sub('REPLACE INTO ^useremailsubscription SET userid = '.qa_get_logged_in_userid().', favoritesonly = b\''.(isset($favonly) ? 1 : 0).'\', email = ($)', $email);

			$message = 'Thank you for subscribing';
			return (true);
		}

		$message = 'The email address was not valid';
		return (false);
	}

	function unsubscribe (&$message)
	{
		qa_db_query_sub('DELETE IGNORE FROM ^useremailsubscription WHERE userid = ($)', qa_get_logged_in_userid());

		$message = 'You have been unsubscribed';
		return (true);
	}

	function verify_email ($email)
	{
		return (preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $email));
	}
};


/*
	Omit PHP closing tag to help avoid accidental output
*/