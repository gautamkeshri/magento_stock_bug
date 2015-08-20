<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
define('BASE_DIR',".");
require_once(BASE_DIR.DS."ndsltool/checkstock.php");
require_once(BASE_DIR.'/app/Mage.php');

ini_set("display_errors", 1);
set_time_limit(0);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$statusobj = new Checkstatus();
$email_msg = $statusobj->main();
var_dump($email_msg);
