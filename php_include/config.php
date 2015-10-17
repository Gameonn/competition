<?php
//error_reporting(0);
$servername = $_SERVER['HTTP_HOST'];
$pathimg=$servername."/";
define("ROOT_PATH",$_SERVER['DOCUMENT_ROOT']);
define("UPLOAD_PATH","http://code-brew.com/projects/rebond/");
define("BASE_PATH","http://code-brew.com/projects/rebond/");

define("SERVER_OFFSET","21600");
$DB_HOST = 'localhost';
$DB_DATABASE = 'codebrew_rebond';
$DB_USER = 'codebrew_super';
$DB_PASSWORD = 'core2duo';


define('SMTP_USER','pargat@code-brew.com');
define('SMTP_EMAIL','pargat@code-brew.com');
define('SMTP_PASSWORD','core2duo');
define('SMTP_NAME','Rebond');
define('SMTP_HOST','mail.code-brew.com');
define('SMTP_PORT','25');