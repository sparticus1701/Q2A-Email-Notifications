<?php

/*
	Plugin Name: Email Notification Revised
	Plugin URI: https://github.com/zakkak/q2a-email-notifications-revised
	Plugin Update Check URI: https://github.com/zakkak/q2a-email-notifications-revised/raw/master/qa-plugin.php
	Plugin Description: Module that allows users to receive emails notifications of new questions
	Plugin Version: 3.0
	Plugin Date: 2013-11-23
	Plugin Author: Foivos S. Zakkak, Walter Williams 
	Plugin Author URI: http://foivos.zakkak.net
	Plugin License: GPLv3
	Plugin Minimum Question2Answer Version: 1.5
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('page', 'qa-user-email-notifications-page.php', 'qa_user_email_notifications_page', 'User Email Notifications');
	qa_register_plugin_module('event', 'qa-user-email-notifications-event.php', 'qa_user_email_notifications_event', 'Email Notifications');


/*
	Omit PHP closing tag to help avoid accidental output
*/
