<?php
/* I certify that this is my own work Danny Gee R01810967 */
/* DATABASE SETTINGS */
// Database hostname, don't change this unless your hostname is different
define('db_host', 'localhost');
// Database username
define('db_user', 'root');
// Database password
define('db_pass', '');
// Database name
define('db_name', 'orderinsystem');

/* GENERAL SETTINGS */
// This will change the title on the website
define('site_name', 'Order IN');
// Currency code, default is USD, you can view the list here: http://cactus.io/resources/toolbox/html-currency-symbol-codes
define('currency_code', '&dollar;');
// Account required for checkout?
define('account_required', 'true');
// The from email that will appear on the customer's order details email
define('mail_from', 'noreply@OrderIN.com');
// Send mail to the customers, etc?
define('mail_enabled', 'true');

?>
