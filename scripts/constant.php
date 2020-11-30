<?php
define( 'APP_NAME', 'Trustco Educations' );
define ( 'APP_SHORT_NAME', 'Trustco Educations' );
define ( 'APP_DIR', '' );
define ( 'DATETIME_MYSQL_FORMAT', 'Y-m-d H:i:s' );
define ( 'DATE_MYSQL_FORMAT', 'Y-m-d' );
define ( 'DATE_SEC_IN_DAY', 86400 );
define ( 'TIME_MYSQL_FORMAT', 'H:i:s' );
define ( 'USER_DATE_FORMAT', 'd/m/Y' );
define ( 'USER_DATE_TIME_FORMAT', 'd M Y H:i' );
define ( 'USER_TIME_FORMAT', 'H:i' );
define ( 'FIELD_INVALID_STYLE', 'border: 1px solid red;' );
define ( 'COMPANY_NAME', 'Trustco Educations' );
define ( 'EXCEPTION_MESSAGE', 'An error has occurred. Please retry.' );
define ( 'UNAUTHORISED_MESSAGE', 'You\'re not authorised to perform this function.' );

//Settings section
define ( 'CURRENCY_SYMBOL', 'N$' );

//Database section
if ( Common::isLiveServer() ) {
	define ( 'MYSQL_HOST', 'localhost' );
	define ( 'MYSQL_USR', 'root' );
	define ( 'MYSQL_PWD', '' );
	define ( 'MYSQL_DB', 'trustco_education1' );
	define( 'APP_DOMAIN', 'http://173/' );
}  else {
	define ( 'MYSQL_HOST', 'localhost' );
    define ( 'MYSQL_USR', 'hope' );
    define ( 'MYSQL_PWD', 'geekS@#5214' );
    define ( 'MYSQL_DB', 'trustco1' );
	define('APP_DOMAIN', 'http://localhost/trustco1/');
}
//SMTP section
define ( 'SMTP_DEBUG', false );
define ( 'SMTP_AUTH', true );
define ( 'SMTP_SECURE', '' );
define ( 'SMTP_HOST', 'mail.geekabyteworld.com' );
define ( 'SMTP_PORT', 587 ); // or 25 or 487
define ( 'SMTP_USERNAME', 'fbstest@myguestsonline.com' );
define ( 'SMTP_PASSWORD', 'Password123' );
define ( 'SMTP_FROM_ADDRESS', 'fbstest@myguestsonline.com' );
?>