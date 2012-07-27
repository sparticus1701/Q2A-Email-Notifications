<?php

/*
	Walter Williams

	File: qa-plugin/user-email-notifications/qa-user-email-notifications-page.php
	Version: 2.0
	Date: 2012-7-27
	Description: Page module class for user email notifications plugin
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
					$subresult = $this->subscribe(qa_post_text('email'), $subresultmsg);
			}
		}
		else if (qa_post_text('optin') == '1')
		{
			qa_captcha_validate_post($captchaerrors);
			if (empty($captchaerrors))
			{
				if (qa_post_text('email'))
					$subresult = $this->unsubscribe(qa_post_text('email'), $subresultmsg);
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
				'request' => array(
					'label' => 'Email address',
					'tags' => 'NAME="email"',
					'value' => '',
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

	function subscribe ($email, &$message)
	{
		if ($this->verify_email($email))
		{
			qa_db_query_sub("CREATE TABLE IF NOT EXISTS ^useremailsubscription (email varchar(80) NOT NULL, registered timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY (email)) ENGINE=InnoDB DEFAULT CHARSET=utf8");
			qa_db_query_sub('INSERT IGNORE INTO ^useremailsubscription SET email = ($)', $email);

			$message = 'Thank you for subscribing';
			return (true);
		}

		$message = 'The email address was not valid';
		return (false);
	}

	function unsubscribe ($email, &$message)
	{
		if ($this->verify_email($email))
		{
			qa_db_query_sub('DELETE IGNORE FROM ^useremailsubscription WHERE email = ($)', $email);

			$message = 'You have been unsubscribed';
			return (true);
		}

		$message = 'The email address was not valid';
		return (false);
	}

	function verify_email ($email)
	{
		return (preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $email));
	}
};


/*
	Omit PHP closing tag to help avoid accidental output
*/