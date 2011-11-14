This allows you to create a page where people can register to receive notifications about new questions and answers.
It also allows you to specify that Experts, Editors, and Moderators will also receive notifications like Admins do.

The user notification list can grow to the point that it becomes very slow to post new questions and answers.  To correct this problem, I have included some
custom code in the qa-external to modify the external mailer to accept BCC.  I included code for a newer version of PHPMailer because we had problems
connecting to GMail with the default code.  If you use the user notifications, it is recommended that you use this external mailer code or modify it
for your particular situation.