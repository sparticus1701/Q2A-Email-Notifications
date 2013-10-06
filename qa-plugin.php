<?php

/*
	Plugin Name: Email Notification
	Plugin URI: https://github.com/sawtoothsoftware/Q2A-Email-Notifications
	Plugin Update Check URI: https://github.com/sawtoothsoftware/Q2A-Email-Notifications/raw/master/qa-plugin.php
	Plugin Description: Module that allows users to receive emails notifications of new questions
	Plugin Version: 3.0
	Plugin Date: 2013-10-05
	Plugin Author: Walter Williams, Foivos S. Zakkak
	Plugin Author URI:
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