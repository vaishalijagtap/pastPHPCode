<?php
//the title of the project
define('APPLICATION_TITLE','Captured Landscape');

//admine email id
define('ADMIN_EMAIL','admin@capturedlandscape.com');
define('SITE_NAME','Captured Landscape Inc');
define('SITE_BASE_PATH','http://61.8.141.221/captured_landscape/');

//print(strrev(strstr(strstr(strrev($_SERVER['PHP_SELF']),'/'),'/')));
$sub = strstr(strrev($_SERVER['PHP_SELF']),'/');
$len = stripos($sub,'/');
define('SITE_URL',$_SERVER['SERVER_NAME'].strrev(strstr(strstr(strrev($_SERVER['PHP_SELF']),'/'),'/')));

define('SITE_URL',$_SERVER['SERVER_NAME'].strrev(strstr(strstr(strrev($_SERVER['PHP_SELF']),'/'),'/')));
define('EMAIL_URL','capturedlandscape.com');
//check the database
define('ONLINE','0');

//Detail of database connectivity
if(ONLINE){
	define('DATABASE_NAME','scott34_wflrealestate');
	define('DATABASE_HOST','localhost');
	define('DATABASE_USERNAME','scott34_scott');
	define('DATABASE_PASSWORD','scott');
}else{
	define('DATABASE_NAME','captured_land');
	define('DATABASE_HOST','localhost');
	define('DATABASE_USERNAME','root');
	define('DATABASE_PASSWORD','console');
}
define('USER_DIR','userData/');
define('THUMBNAIL_DIR','thumbnail');

define('MAIN_IMAGE_WIDTH','200');
define('MAIN_IMAGE_HEIGHT','200');

define('MAIN_THUMBNAIL_WIDTH','75');
define('MAIN_THUMBNAIL_HEIGHT','75'); 

define('LOGO_WIDTH','100');
define('LOGO_HEIGHT','100');
 
define('THUMBNAI_EXT','gif');
$connect = mysql_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD) or die ("Please Check your server connection.");
mysql_select_db(DATABASE_NAME,$connect)or die(mysql_error());
?>