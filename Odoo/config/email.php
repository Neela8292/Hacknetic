<?php
// SMTP Configuration for PHPMailer
// IMPORTANT: For security, do NOT commit sensitive credentials directly to version control.
// Consider using environment variables or a more secure configuration management system in production.

define('SMTP_HOST', 'smtp.gmail.com'); // Corrected: For Gmail, this is the host
define('SMTP_USERNAME', 'bindupriya.bikki@gmail.com'); // Your Gmail address
define('SMTP_PASSWORD', 'wjmi enwp anzx psdo'); // Your Gmail App Password (or regular password if 2FA is off and allowed)
define('SMTP_PORT', 587); // For TLS
define('SMTP_ENCRYPTION', 'tls'); // Use 'ssl' for port 465, 'tls' for port 587

define('MAIL_FROM_EMAIL', 'bindupriya.bikki@gmail.com'); // Sender email address
define('MAIL_FROM_NAME', 'SkillSwap Platform'); // Sender name

// You can add other email settings here if needed
?>
