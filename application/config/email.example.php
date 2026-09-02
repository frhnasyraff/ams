<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| Copy this file to email.php and provide credentials locally when SMTP is used.
| Never commit the real email.php file.
*/
$config['protocol'] = 'smtp';
$config['smtp_host'] = 'smtp.example.com';
$config['smtp_port'] = 587;
$config['smtp_user'] = 'username@example.com';
$config['smtp_pass'] = 'replace-with-local-password';
$config['smtp_crypto'] = 'tls';
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
