<?php
require_once("global.php");
SlightPHP::setDebug(true);
SlightPHP::setSplitFlag("-_");

SDb::setConfigFile("db.ini");


print_r(SDb::getConfig("main","main"));
print_r(SDb::getConfig("user","query"));
print_r(SDb::getConfig("blog","main"));
print_r(SDb::getConfig("blog","query"));
$db_config = SDb::getConfig("test");

$db = SDb::getDbEngine("mysql");
//$db = SDb::getDbEngine("pdo_mysql");
if(!$db){
	die("DbEngine not exits");
}
$db->init($db_config);
?>
