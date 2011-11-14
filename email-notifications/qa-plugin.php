<?php

/*
	Email Notifications 1.0.0 (c) 2011, Sawtooth Software, Inc.

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/

/*
	Plugin Name: Email Notification
	Plugin URI: 
	Plugin Description: Module that allows users to receive emails notifications of new questions
	Plugin Version: 1.0
	Plugin Date: 2011-11-14
	Plugin Author: Walter Williams
	Plugin Author URI: 
	Plugin License: GPLv2
	Plugin Minimum Question2Answer Version: 1.4
*/


	if (!defined('QA_VERSION')) { // don't allow this page to be requested directly from browser
		header('Location: ../../');
		exit;
	}


	qa_register_plugin_module('page', 'email-notifications-page.php', 'email_notifications_page', 'User Email Notifications');
	qa_register_plugin_module('event', 'email-notifications-event.php', 'email_notifications_event', 'Email Notifications');


/*
	Omit PHP closing tag to help avoid accidental output
*/