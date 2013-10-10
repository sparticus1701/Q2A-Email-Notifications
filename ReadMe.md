#Q2A Email Notifications Revised

This plugin allows you to create a page where people can register to
receive notifications about new questions and answers.  It also allows
you to specify that Experts, Editors, and Moderators will also receive
notifications like Admins do.

The user notification list can grow to the point that it becomes very
slow to post new questions and answers.  To correct this problem, I
have included some custom code in the qa-external to modify the
external mailer to accept BCC.  I included code for a newer version of
PHPMailer because we had problems connecting to GMail with the default
code.  If you use the user notifications, it is recommended that you
use this external mailer code or modify it for your particular
situation.

Only logged in users can subscribe, since we use the userid as a key
instead of the email.

This plugin was inspired and based on https://github.com/sawtoothsoftware/Q2A-Email-Notifications 

### LICENSE
    Q2A Email Notifications
    Copyright (C) 2011-13  Foivos S. Zakkak
                           Walter Williams
    
    https://github.com/zakkak/q2a-email-notifications-revised
    
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.
    
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
