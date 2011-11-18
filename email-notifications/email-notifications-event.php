<?php

/*
	Walter Williams

	File: qa-plugin/user-email-notifications/qa-user-email-notifications-event.php
	Version: 1.0
	Date: 2011-10-20
	Description: Event module class for user email notifications plugin
*/


require_once QA_INCLUDE_DIR.'qa-db-selects.php';
require_once QA_INCLUDE_DIR.'qa-app-users.php';
require_once QA_INCLUDE_DIR.'qa-app-format.php';
require_once QA_INCLUDE_DIR.'qa-app-emails.php';
require_once QA_INCLUDE_DIR.'qa-app-posts.php';
require_once QA_INCLUDE_DIR.'qa-util-emailer.php';
require_once QA_BASE_DIR.'qa-config.php';


class email_notifications_event
{
	function process_event ($event, $userid, $handle, $cookieid, $params)
	{
		$users=qa_db_select_with_pending(qa_db_users_from_level_selectspec(QA_USER_LEVEL_EXPERT));

		$emailsubscriptions = false;
		if ($this->user_email_notification_table_exists())
			$emailsubscriptions = qa_db_read_all_values(qa_db_query_sub("SELECT email from ^useremailsubscription"));

		if ($event == 'q_post')
		{
			$subject = 'New ^site_title question: ^q_title';
			foreach ($users as $user) {
				$role = $user['level'];

				if ($role == QA_USER_LEVEL_ADMIN || $role == QA_USER_LEVEL_SUPER)
					continue;

				if ($role == QA_USER_LEVEL_EXPERT && ((int)qa_opt('expert_emailnotifications_enabled')) == 0)
					continue;
				if ($role == QA_USER_LEVEL_EDITOR && ((int)qa_opt('editor_emailnotifications_enabled')) == 0)
					continue;
				if ($role == QA_USER_LEVEL_MODERATOR && ((int)qa_opt('moderator_emailnotifications_enabled')) == 0)
					continue;

				qa_send_notification($user['userid'], null, null, $subject, qa_lang('emails/q_posted_body'), array(
					'^q_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
					'^q_title' => $params['title'], // don't censor title or content since we want the admin to see bad words
					'^q_content' => $params['text'],
					'^url' => qa_path(qa_q_request($params['postid'], $params['title']), null, qa_opt('site_url')),
				));
			}

			if ($emailsubscriptions) // email those in the database
			{
				$body = "A question on ^site_title has been asked by ^q_handle:\n\nThe question is:\n\n^open^q_title^close\n\n^open^q_content^close\n\nIf you would like to view this question:\n\n^url\n\nThank you,\n\n^site_title";
				$subject = 'New ^site_title question: ^q_title';
				$subs = array(
					'^q_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
					'^q_title' => $params['title'], // don't censor title or content since we want the admin to see bad words
					'^q_content' => $params['text'],
					'^url' => qa_path(qa_q_request($params['postid'], $params['title']), null, qa_opt('site_url')),
					'^site_title' => qa_opt('site_title'),
					'^open' => "\n",
					'^close' => "\n",
				);

				for ($i = 0; $i < count($emailsubscriptions); $i++)
				{
					$bcclist = array();
					for ($j = 0; $j < 75 && $i < count($emailsubscriptions); $j++, $i++)
					{
						$bcclist[] = $emailsubscriptions[$i];
					}

					qa_send_email(array(
						'fromemail' => qa_opt('from_email'),
						'fromname' => qa_opt('site_title'),
						'bcclist' => $bcclist,
						'subject' => strtr($subject, $subs),
						'body' => strtr($body, $subs),
						'html' => false,
					));
				}
			}
		}
		else if ($event == 'a_post')
		{
			$body = "A question on ^site_title has been answered by ^a_handle:\n\n^open^a_content^close\n\nThe question was:\n\n^open^q_title^close\n\nIf you would like to view this question:\n\n^url\n\nThank you,\n\n^site_title";
			$subject = 'New ^site_title answer to: ^q_title';

			$parentpost=qa_post_get_full($params['parentid']);

			foreach ($users as $user) {
				$role = $user['level'];

				if (($role == QA_USER_LEVEL_ADMIN || $role == QA_USER_LEVEL_SUPER) && ((int)qa_opt('admin_emailnotifications_enabled')) == 0)
					continue;

				if ($role == QA_USER_LEVEL_EXPERT && ((int)qa_opt('expert_emailnotifications_enabled')) == 0)
					continue;
				if ($role == QA_USER_LEVEL_EDITOR && ((int)qa_opt('editor_emailnotifications_enabled')) == 0)
					continue;
				if ($role == QA_USER_LEVEL_MODERATOR && ((int)qa_opt('moderator_emailnotifications_enabled')) == 0)
					continue;

				qa_send_notification($user['userid'], null, null, $subject, $body, array(
					'^a_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
					'^q_title' => $parentpost['title'], // don't censor title or content since we want the admin to see bad words
					'^a_content' => $params['text'],
					'^url' => qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid'])),
				));
			}

			if ($emailsubscriptions) // email those in the database
			{
				$subs = array(
					'^a_handle' => isset($handle) ? $handle : qa_lang('main/anonymous'),
					'^q_title' => $parentpost['title'], // don't censor title or content since we want the admin to see bad words
					'^a_content' => $params['text'],
					'^url' => qa_path(qa_q_request($params['parentid'], $parentpost['title']), null, qa_opt('site_url'), null, qa_anchor('A', $params['postid'])),
					'^site_title' => qa_opt('site_title'),
					'^open' => "\n",
					'^close' => "\n",
				);

				for ($i = 0; $i < count($emailsubscriptions); $i++)
				{
					$bcclist = array();
					for ($j = 0; $j < 75 && $i < count($emailsubscriptions); $j++, $i++)
					{
						$bcclist[] = $emailsubscriptions[$i];
					}

					qa_send_email(array(
						'fromemail' => qa_opt('from_email'),
						'fromname' => qa_opt('site_title'),
						'bcclist' => $bcclist,
						'subject' => strtr($subject, $subs),
						'body' => strtr($body, $subs),
						'html' => false,
					));
				}
			}
		}
	}

	function admin_form(&$qa_content)
	{
		$saved=false;

		if (qa_clicked('emailnotifications_save_button')) {
			qa_opt('admin_emailnotifications_enabled', (int)qa_post_text('admin_emailnotifications_enabled_field'));
			qa_opt('expert_emailnotifications_enabled', (int)qa_post_text('expert_emailnotifications_enabled_field'));
			qa_opt('editor_emailnotifications_enabled', (int)qa_post_text('editor_emailnotifications_enabled_field'));
			qa_opt('moderator_emailnotifications_enabled', (int)qa_post_text('moderator_emailnotifications_enabled_field'));
			$saved=true;
		}

		return array(
			'ok' => $saved ? 'Email Notifications settings saved' : null,

			'fields' => array(
				array(
					'label' => 'Allow Experts to receive emails about new questions & answers',
					'type' => 'checkbox',
					'value' => (int)qa_opt('expert_emailnotifications_enabled'),
					'tags' => 'NAME="expert_emailnotifications_enabled_field" ID="expert_emailnotifications_enabled_field"',
				),
				array(
					'label' => 'Allow Editors to receive emails about new questions & answers',
					'type' => 'checkbox',
					'value' => (int)qa_opt('editor_emailnotifications_enabled'),
					'tags' => 'NAME="editor_emailnotifications_enabled_field" ID="editor_emailnotifications_enabled_field"',
				),
				array(
					'label' => 'Allow Moderators to receive emails about new questions & answers',
					'type' => 'checkbox',
					'value' => (int)qa_opt('moderator_emailnotifications_enabled'),
					'tags' => 'NAME="moderator_emailnotifications_enabled_field" ID="moderator_emailnotifications_enabled_field"',
				),
				array(
					'label' => 'Allow Admins to receive emails about new answers (questions are handled elsewhere)',
					'type' => 'checkbox',
					'value' => (int)qa_opt('admin_emailnotifications_enabled'),
					'tags' => 'NAME="admin_emailnotifications_enabled_field" ID="admin_emailnotifications_enabled_field"',
				),
			),

			'buttons' => array(
				array(
					'label' => 'Save Changes',
					'tags' => 'NAME="emailnotifications_save_button"',
				),
			),
		);
	}

	function user_email_notification_table_exists ()
	{
		$res = qa_db_query_sub("SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '". QA_MYSQL_DATABASE ."' AND table_name = '^useremailsubscription'");
		return mysql_result($res, 0) == 1;
	}
};


/*
	Omit PHP closing tag to help avoid accidental output
*/