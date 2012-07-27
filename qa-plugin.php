<?php

/*
	Plugin Name: Email Notification
	Plugin URI: 
	Plugin Description: Module that allows special users to receive emails notifications of new questions
	Plugin Version: 2.0
	Plugin Date: 2012-7-27
	Plugin Author: Walter Williams
	Plugin Author URI: 
	Plugin License: 
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