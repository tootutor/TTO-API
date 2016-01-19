<?php
/////////////////////// Header Section
// Allow from any origin
/*
if (isset($_SERVER['HTTP_ORIGIN'])) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
		header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
	}

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
		header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
	}
}
*/
//header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

/////////////////////// Config Section
require_once 'config/config.php';

/////////////////////// PHPMailer Section
require_once 'vendor/PHPMailer/PHPMailerAutoload.php';

/////////////////////// Database Section - https://github.com/ajaxray/static-pdo
require_once 'vendor/SimplePDO/Db.php';
Db::setConnectionInfo(DBNAME, DBUSER, DBPASS, DBTYPE, DBHOST);
Db::execute('SET CHARACTER SET utf8');

/////////////////////// Static class for TooTutor Online
require_once 'vendor/TooTutor/TTO.php';
require_once 'vendor/TooTutor/TTOMail.php';

/////////////////////// Restler Section
require_once 'vendor/restler.php';
use Luracast\Restler\Restler;
Defaults::$crossOriginResourceSharing = true;
Defaults::$accessControlAllowOrigin = '*';

$r = new Restler();
//$r->addAPIClass('Test');
$r->addAPIClass('Explorer');
//$r->setAPIVersion(1);
$r->addAuthenticationClass('Auth');
$r->addAPIClass('App');
$r->addAPIClass('User');
$r->addAPIClass('Bank');
$r->addAPIClass('Coin');
$r->addAPIClass('Category');
$r->addAPIClass('Course');
$r->addAPIClass('CourseSection');
$r->addAPIClass('Item');
$r->addAPIClass('CourseItem');
$r->addAPIClass('UserCourse');
$r->addAPIClass('UserSection');
$r->addAPIClass('UserItem');
$r->addAPIClass('UserItemDetail');
$r->addAPIClass('UserCourseItem');
$r->addAPIClass('Comment');
$r->addAPIClass('Notification');
$r->addAPIClass('Email');
$r->handle();

////////////////////// HTTP return code - http://www.restapitutorial.com/httpstatuscodes.html